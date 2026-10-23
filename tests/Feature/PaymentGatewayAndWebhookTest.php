<?php

namespace Tests\Feature;

use App\Models\Guardian;
use App\Models\MenuItem;
use App\Models\MenuVersion;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderSchedule;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\PaymentWebhook;
use App\Models\Permission;
use App\Models\Restaurant;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolLocation;
use App\Models\SchoolMealPolicy;
use App\Models\SchoolShift;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PaymentGatewayAndWebhookTest extends TestCase
{
    use DatabaseTransactions;

    protected User $parentUser;
    protected Guardian $guardian;
    protected School $school;
    protected SchoolLocation $location;
    protected Restaurant $restaurant;
    protected Student $student;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Roles & Permissions
        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['guard_name' => 'web']);
        $perms = ['orders.view', 'orders.create'];
        foreach ($perms as $pName) {
            $p = Permission::firstOrCreate(['name' => $pName]);
            $parentRole->permissions()->syncWithoutDetaching([$p->id]);
        }

        // 2. School with allow_cod = false by default
        $this->school = School::create([
            'code' => 'SCH-PAY-TEST-' . uniqid(),
            'name' => 'Payment Gateway Academy',
            'level' => 'primary',
            'status' => 'active',
        ]);

        SchoolMealPolicy::create([
            'school_id' => $this->school->id,
            'default_cutoff_minutes' => 120,
            'allowed_meal_types' => ['lunch'],
            'allow_cod' => false,
        ]);

        $shift = SchoolShift::create([
            'school_id' => $this->school->id,
            'name' => 'Regular Lunch',
            'start_time' => '11:45:00',
            'end_time' => '13:00:00',
        ]);

        $this->location = SchoolLocation::create([
            'school_id' => $this->school->id,
            'name' => 'Dining Hall',
        ]);

        // 3. User, Guardian & Student
        $this->parentUser = User::create([
            'full_name' => 'Payment Test Parent',
            'email' => 'parent.payway.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);
        $this->parentUser->assignRole('parent');
        $this->guardian = Guardian::create(['user_id' => $this->parentUser->id]);

        $merchantOwner = User::create([
            'full_name' => 'Payment Merchant Owner',
            'email' => 'merchant.pay.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);

        $this->restaurant = Restaurant::create([
            'code' => 'REST-PAY-' . uniqid(),
            'name' => 'Gateway Kitchen',
            'owner_user_id' => $merchantOwner->id,
            'status' => 'active',
            'commission_default_pct' => 10.00,
        ]);

        $this->student = Student::create([
            'student_code' => 'STU-PAY-' . uniqid(),
            'school_id' => $this->school->id,
            'school_shift_id' => $shift->id,
            'full_name' => 'Serey Pay',
            'grade_level' => 'Grade 5',
            'class_name' => '5-B',
            'status' => 'active',
        ]);

        $this->student->guardians()->attach($this->guardian->id, [
            'is_primary' => true,
            'can_order' => true,
        ]);

        // 4. Create an Order in pending status
        $this->order = Order::create([
            'order_number' => 'ORD-PAY-' . uniqid(),
            'guardian_id' => $this->guardian->id,
            'student_id' => $this->student->id,
            'school_id' => $this->school->id,
            'restaurant_id' => $this->restaurant->id,
            'status' => 'pending',
            'subtotal_minor' => 700,
            'total_minor' => 700,
            'currency' => 'USD',
            'placed_at' => now(),
        ]);
    }

    public function test_idempotency_middleware_requires_header_on_order_and_payment_initiation(): void
    {
        // 1. Without Idempotency-Key on /api/v1/payments/initiate -> must return 422
        $response = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/payments/initiate', [
                'order_id' => $this->order->id,
                'provider' => 'KHQR',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['idempotency_key']);

        // 2. Without Idempotency-Key on /api/v1/orders -> must return 422
        $orderResponse = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/orders', [
                'student_id' => $this->student->id,
                'restaurant_id' => $this->restaurant->id,
                'school_location_id' => $this->location->id,
                'items' => [],
            ]);

        $orderResponse->assertStatus(422)
            ->assertJsonValidationErrors(['idempotency_key']);
    }

    public function test_idempotency_middleware_prevents_duplicate_charge_and_returns_cached_response(): void
    {
        $idempotencyKey = 'idem-unique-' . uniqid();

        $payload = [
            'order_id' => $this->order->id,
            'provider' => 'KHQR',
            'method' => 'bakong_qr',
        ];

        // First attempt -> processes and caches response
        $firstResponse = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/payments/initiate', $payload, [
                'Idempotency-Key' => $idempotencyKey,
            ]);

        $firstResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $paymentId1 = $firstResponse->json('data.payment_id');
        $this->assertNotNull($paymentId1);

        // Second attempt with exact same key and payload -> returns cached response (no duplicate Payment record)
        $secondResponse = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/payments/initiate', $payload, [
                'Idempotency-Key' => $idempotencyKey,
            ]);

        $secondResponse->assertStatus(201)
            ->assertHeader('X-Idempotency-Hit', 'true')
            ->assertJsonPath('data.payment_id', $paymentId1);

        $this->assertEquals(1, Payment::where('order_id', $this->order->id)->count());

        // Third attempt with same key but altered payload -> rejects with 422 payload mismatch
        $conflictResponse = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/payments/initiate', array_merge($payload, ['provider' => 'ABA']), [
                'Idempotency-Key' => $idempotencyKey,
            ]);

        $conflictResponse->assertStatus(422)
            ->assertJsonValidationErrors(['idempotency_key']);
    }

    public function test_khqr_bakong_payment_initiation_and_emvco_payload(): void
    {
        $response = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/payments/initiate', [
                'order_id' => $this->order->id,
                'provider' => 'KHQR',
            ], [
                'Idempotency-Key' => 'idem-khqr-' . uniqid(),
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.provider', 'KHQR')
            ->assertJsonPath('data.status', 'pending');

        $ref = $response->json('data.provider_reference');
        $this->assertStringStartsWith('KHQR-', $ref);
        $this->assertNotEmpty($response->json('data.qr_payload'));
        $this->assertStringStartsWith('bakong://', $response->json('data.deep_link'));

        // Check Payment and PaymentTransaction created in database
        $payment = Payment::where('provider_reference', $ref)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(700, $payment->amount_minor);
        $this->assertEquals('pending', $payment->status);

        $tx = PaymentTransaction::where('payment_id', $payment->id)->first();
        $this->assertNotNull($tx);
        $this->assertEquals('QR_GENERATED', $tx->response_code);
    }

    public function test_aba_payway_and_acleda_gateways(): void
    {
        // 1. ABA PayWay
        $abaResponse = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/payments/initiate', [
                'order_id' => $this->order->id,
                'provider' => 'ABA',
            ], [
                'Idempotency-Key' => 'idem-aba-' . uniqid(),
            ]);

        $abaResponse->assertStatus(201)
            ->assertJsonPath('data.provider', 'ABA')
            ->assertJsonPath('data.status', 'pending');

        $this->assertStringStartsWith('ABA-', $abaResponse->json('data.provider_reference'));
        $this->assertNotEmpty($abaResponse->json('data.hash'));

        // 2. ACLEDA X-Pay
        $aclResponse = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/payments/initiate', [
                'order_id' => $this->order->id,
                'provider' => 'ACLEDA',
            ], [
                'Idempotency-Key' => 'idem-acl-' . uniqid(),
            ]);

        $aclResponse->assertStatus(201)
            ->assertJsonPath('data.provider', 'ACLEDA')
            ->assertJsonPath('data.status', 'pending');

        $this->assertStringStartsWith('ACL-', $aclResponse->json('data.provider_reference'));
        $this->assertNotEmpty($aclResponse->json('data.xpay_token'));
    }

    public function test_cod_payment_initiation_enforces_school_policy_and_confirms_order(): void
    {
        // 1. School meal policy allow_cod is FALSE by default -> must fail with 422
        $failedResponse = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/payments/initiate', [
                'order_id' => $this->order->id,
                'provider' => 'COD',
            ], [
                'Idempotency-Key' => 'idem-cod-fail-' . uniqid(),
            ]);

        $failedResponse->assertStatus(422)
            ->assertJsonValidationErrors(['provider']);

        // 2. Update school policy to allow COD
        $this->school->mealPolicy->update(['allow_cod' => true]);

        $successResponse = $this->actingAs($this->parentUser)
            ->postJson('/api/v1/payments/initiate', [
                'order_id' => $this->order->id,
                'provider' => 'COD',
            ], [
                'Idempotency-Key' => 'idem-cod-succ-' . uniqid(),
            ]);

        $successResponse->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.provider', 'COD')
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.order_status', 'confirmed');

        // Verify order is now confirmed for kitchen preparation while marked for COD reconciliation
        $order = $this->order->fresh();
        $this->assertEquals('confirmed', $order->status);
        $this->assertStringContainsString('COD: Payable on delivery', $order->notes);

        $payment = Payment::where('order_id', $this->order->id)->where('provider', 'COD')->first();
        $this->assertNotNull($payment);
        $this->assertEquals('pending', $payment->status);
    }

    public function test_signed_webhook_receiver_verifies_signature_and_confirms_order(): void
    {
        // Setup initial KHQR pending payment
        $payment = Payment::create([
            'order_id' => $this->order->id,
            'provider' => 'KHQR',
            'method' => 'bakong_qr',
            'status' => 'pending',
            'amount_minor' => $this->order->total_minor,
            'currency' => 'USD',
            'provider_reference' => 'REF-KHQR-TEST-' . uniqid(),
        ]);

        $secret = config('services.khqr.secret', 'khqr_webhook_secret_key');
        $eventId = 'EVT-' . uniqid();

        $webhookPayload = [
            'event_id' => $eventId,
            'event_type' => 'payment.completed',
            'provider_reference' => $payment->provider_reference,
            'payment_id' => $payment->id,
            'status' => 'SUCCESS',
            'amount_minor' => 700,
            'currency' => 'USD',
        ];

        $rawBody = json_encode($webhookPayload);

        // 1. Invalid signature attempt -> must reject with 401
        $invalidSigResponse = $this->call(
            'POST',
            '/api/v1/webhooks/payments/khqr',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_SIGNATURE' => 'invalid_forged_signature',
            ],
            $rawBody
        );

        $invalidSigResponse->assertStatus(401);
        $this->assertDatabaseHas('payment_webhooks', [
            'provider' => 'KHQR',
            'signature_valid' => false,
            'status' => 'failed',
        ]);

        // 2. Valid HMAC SHA256 signature -> must process successfully
        $validSignature = hash_hmac('sha256', $rawBody, $secret);

        $validResponse = $this->call(
            'POST',
            '/api/v1/webhooks/payments/khqr',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_SIGNATURE' => $validSignature,
            ],
            $rawBody
        );

        $validResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', 'paid');

        // Check payment updated to paid with paid_at timestamp
        $freshPayment = $payment->fresh();
        $this->assertEquals('paid', $freshPayment->status);
        $this->assertNotNull($freshPayment->paid_at);

        // Check order status transitioned to confirmed
        $freshOrder = $this->order->fresh();
        $this->assertEquals('confirmed', $freshOrder->status);

        // Check PaymentTransaction recorded
        $this->assertDatabaseHas('payment_transactions', [
            'payment_id' => $payment->id,
            'status' => 'paid',
            'amount_minor' => 700,
            'provider_transaction_id' => $eventId,
        ]);

        // Check PaymentWebhook recorded as processed
        $this->assertDatabaseHas('payment_webhooks', [
            'provider' => 'KHQR',
            'event_id' => $eventId,
            'status' => 'processed',
            'signature_valid' => true,
        ]);
    }

    public function test_webhook_receiver_is_idempotent_on_duplicate_callbacks(): void
    {
        $payment = Payment::create([
            'order_id' => $this->order->id,
            'provider' => 'ABA',
            'method' => 'aba_payway',
            'status' => 'pending',
            'amount_minor' => $this->order->total_minor,
            'currency' => 'USD',
            'provider_reference' => 'REF-ABA-DUP-' . uniqid(),
        ]);

        $apiKey = config('services.aba_payway.api_key', 'aba_secret_key');
        $eventId = 'EVT-ABA-' . uniqid();

        $webhookPayload = [
            'event_id' => $eventId,
            'tran_id' => $eventId,
            'provider_reference' => $payment->provider_reference,
            'payment_id' => $payment->id,
            'status' => 'SUCCESS',
            'amount_minor' => 700,
        ];

        $rawBody = json_encode($webhookPayload);
        $validSignature = base64_encode(hash_hmac('sha512', $rawBody, $apiKey, true));

        // First callback
        $res1 = $this->call(
            'POST',
            '/api/v1/webhooks/payments/aba',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_SIGNATURE' => $validSignature,
            ],
            $rawBody
        );
        $res1->assertStatus(200)->assertJsonPath('status', 'paid');

        // Duplicate callback with same event_id
        $res2 = $this->call(
            'POST',
            '/api/v1/webhooks/payments/aba',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_SIGNATURE' => $validSignature,
            ],
            $rawBody
        );
        $res2->assertStatus(200)->assertJsonPath('status', 'already_processed');

        // Only one transaction should be logged
        $this->assertEquals(1, PaymentTransaction::where('payment_id', $payment->id)->count());
    }
}
