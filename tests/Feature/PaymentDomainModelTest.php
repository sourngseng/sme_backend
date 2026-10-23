<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Guardian;
use App\Models\IdempotencyKey;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\PaymentWebhook;
use App\Models\Restaurant;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PaymentDomainModelTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'full_name' => 'Payment Test User',
            'email' => 'user.pay.' . uniqid() . '@example.com',
            'password' => 'Password123!',
            'status' => 'active',
        ]);

        $guardian = Guardian::create(['user_id' => $this->user->id]);

        $school = School::create([
            'code' => 'SCH-PAY-' . uniqid(),
            'name' => 'Payment Academy',
            'level' => 'primary',
            'status' => 'active',
        ]);

        $student = Student::create([
            'student_code' => 'STU-PAY-' . uniqid(),
            'school_id' => $school->id,
            'full_name' => 'Pay Student',
            'status' => 'active',
        ]);

        $restaurant = Restaurant::create([
            'code' => 'REST-PAY-' . uniqid(),
            'name' => 'Pay Kitchen',
            'owner_user_id' => $this->user->id,
            'status' => 'active',
        ]);

        $this->order = Order::create([
            'order_number' => 'ORD-PAY-' . uniqid(),
            'guardian_id' => $guardian->id,
            'student_id' => $student->id,
            'school_id' => $school->id,
            'restaurant_id' => $restaurant->id,
            'status' => 'pending',
            'subtotal_minor' => 1000,
            'total_minor' => 1000,
            'currency' => 'USD',
        ]);
    }

    public function test_payment_creation_and_auditable_transitions(): void
    {
        $this->actingAs($this->user);

        $payment = Payment::create([
            'order_id' => $this->order->id,
            'provider' => 'KHQR',
            'method' => 'bakong_qr',
            'status' => 'pending',
            'amount_minor' => 1000,
            'currency' => 'USD',
            'provider_reference' => 'REF-KHQR-' . uniqid(),
        ]);

        $this->assertNotNull($payment->id);
        $this->assertEquals(1000, $payment->amount_minor);
        $this->assertEquals('pending', $payment->status);
        $this->assertEquals($this->order->id, $payment->order->id);

        // Transition to paid
        $payment->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);

        $this->assertEquals('paid', $payment->fresh()->status);
        $this->assertNotNull($payment->fresh()->paid_at);

        // Verify audit log recorded status change
        $audit = AuditLog::where('auditable_type', Payment::class)
            ->where('auditable_id', $payment->id)
            ->whereIn('action', ['updated', 'status_updated'])
            ->first();

        $this->assertNotNull($audit);
        $this->assertContains($audit->action, ['updated', 'status_updated']);
        $this->assertArrayHasKey('status', $audit->new_values ?? []);
    }

    public function test_payment_transaction_relationship_and_payload_casting(): void
    {
        $payment = Payment::create([
            'order_id' => $this->order->id,
            'provider' => 'ABA',
            'method' => 'aba_payway',
            'status' => 'pending',
            'amount_minor' => 2500,
            'currency' => 'USD',
        ]);

        $tx = PaymentTransaction::create([
            'payment_id' => $payment->id,
            'transaction_type' => 'charge',
            'amount_minor' => 2500,
            'provider_transaction_id' => 'TX-ABA-' . uniqid(),
            'status' => 'paid',
            'response_code' => '00',
            'response_payload' => [
                'auth_code' => '123456',
                'stan' => '998877',
                'raw_status' => 'SUCCESS',
            ],
        ]);

        $this->assertNotNull($tx->id);
        $this->assertEquals($payment->id, $tx->payment->id);
        $this->assertCount(1, $payment->transactions);
        $this->assertEquals('123456', $tx->fresh()->response_payload['auth_code']);
    }

    public function test_payment_webhook_unique_constraint_on_provider_and_event(): void
    {
        $eventId = 'EVT-' . uniqid();

        $webhook = PaymentWebhook::create([
            'provider' => 'KHQR',
            'event_id' => $eventId,
            'event_type' => 'payment.completed',
            'payload' => ['txn_id' => 'TX123', 'status' => 'SUCCESS'],
            'signature_valid' => true,
            'status' => 'received',
        ]);

        $this->assertNotNull($webhook->id);
        $this->assertTrue($webhook->signature_valid);
        $this->assertEquals('TX123', $webhook->payload['txn_id']);

        // Duplicate event with same provider must throw constraint violation
        $this->expectException(QueryException::class);
        PaymentWebhook::create([
            'provider' => 'KHQR',
            'event_id' => $eventId,
            'event_type' => 'payment.completed',
            'payload' => ['duplicate' => true],
        ]);
    }

    public function test_idempotency_key_storage_and_expiration(): void
    {
        $keyString = 'idem-' . uniqid();
        $expiresAt = Carbon::now()->addHours(24);

        $idemKey = IdempotencyKey::create([
            'key' => $keyString,
            'user_id' => $this->user->id,
            'request_hash' => hash('sha256', 'POST|/api/v1/orders|payload'),
            'response_code' => 201,
            'response_body' => [
                'success' => true,
                'data' => ['order_id' => $this->order->id],
            ],
            'expires_at' => $expiresAt,
        ]);

        $this->assertEquals($keyString, $idemKey->key);
        $this->assertEquals($this->user->id, $idemKey->user->id);
        $this->assertEquals(201, $idemKey->response_code);
        $this->assertTrue($idemKey->response_body['success']);

        $loaded = IdempotencyKey::find($keyString);
        $this->assertNotNull($loaded);
        $this->assertEquals($this->order->id, $loaded->response_body['data']['order_id']);
    }
}
