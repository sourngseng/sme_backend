<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
    ];

    /**
     * Canonical RFC 4122 Nil UUID for system-level entity audits.
     */
    public const SYSTEM_UUID = '00000000-0000-0000-0000-000000000000';

    /**
     * Retrieve a setting value by key with fallback default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Store or update a setting value.
     */
    public static function set(string $key, mixed $value, ?string $group = null, ?string $type = null, ?string $label = null, ?string $description = null): self
    {
        $setting = static::firstOrNew(['key' => $key]);

        if ($type !== null) {
            $setting->type = $type;
        } elseif (! $setting->type) {
            if (is_bool($value) || in_array($value, ['true', 'false', '0', '1'], true) && in_array($key, ['maintenance_mode', 'enable_order_auto_approval', 'enable_dietary_allergen_warnings', 'allow_same_day_cancellation', 'enable_weekend_ordering', 'enable_student_qr_scan', 'allow_custom_meal_notes', 'auto_payout_enabled', 'require_kitchen_hygiene_cert', 'allow_merchant_instant_item_toggle', 'kitchen_tablet_kds_mode', 'session_single_device_login', 'require_two_factor_staff', 'sms_notifications_enabled', 'order_status_sms_alerts', 'low_balance_sms_threshold_enabled', 'daily_financial_summary_sms', 'system_error_sms_broadcast', 'auto_clear_expired_carts'])) {
                $setting->type = 'boolean';
            } elseif (is_bool($value)) {
                $setting->type = 'boolean';
            } elseif (is_int($value)) {
                $setting->type = 'integer';
            } elseif (is_float($value)) {
                $setting->type = 'float';
            } elseif (is_array($value)) {
                $setting->type = 'json';
            } else {
                $setting->type = 'string';
            }
        }

        if (in_array($setting->type, ['boolean', 'bool'])) {
            $value = ($value === '1' || $value === 'true' || $value === true || $value === 1) ? '1' : '0';
        }

        $setting->value = is_array($value) ? json_encode($value) : (string) $value;
        if ($group !== null) {
            $setting->group = $group;
        }
        if ($label !== null) {
            $setting->label = $label;
        }
        if ($description !== null) {
            $setting->description = $description;
        }

        $setting->save();

        return $setting;
    }

    /**
     * Cast string value from database to proper PHP type.
     */
    public static function castValue(mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $value,
            'float', 'double' => (float) $value,
            'json', 'array' => is_string($value) ? json_decode($value, true) : (array) $value,
            default => (string) $value,
        };
    }

    /**
     * Retrieve all settings grouped by group name.
     *
     * @return array<string, \Illuminate\Database\Eloquent\Collection<int, SystemSetting>>
     */
    public static function getAllGrouped(): array
    {
        if (static::count() === 0) {
            static::seedDefaults();
        }

        $all = static::orderBy('id')->get();
        $grouped = [];

        foreach ($all as $item) {
            $grouped[$item->group][] = $item;
        }

        return $grouped;
    }

    /**
     * Seed comprehensive default settings for national school meal e-commerce.
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            // 1. General & Platform Identity
            [
                'key' => 'platform_name',
                'value' => 'SchoolMeal National E-Commerce',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Platform Brand Name',
                'description' => 'Official national portal brand display title',
            ],
            [
                'key' => 'platform_tagline',
                'value' => 'National Student Nutrition & School Catering Platform',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Platform Tagline & Slogan',
                'description' => 'Mission statement and portal subtitle',
            ],
            [
                'key' => 'support_email',
                'value' => 'support@schoolmeal.gov.kh',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Official Support Email',
                'description' => 'Direct contact for schools, kitchens, and parents',
            ],
            [
                'key' => 'support_phone',
                'value' => '+855 23 888 999',
                'group' => 'general',
                'type' => 'string',
                'label' => 'National Support Hotline',
                'description' => 'Toll-free customer care hotline',
            ],
            [
                'key' => 'primary_timezone',
                'value' => 'Asia/Phnom_Penh',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Primary System Timezone',
                'description' => 'Base timezone for all kitchen schedules and order delivery windows',
            ],
            [
                'key' => 'default_currency',
                'value' => 'KHR',
                'group' => 'general',
                'type' => 'string',
                'label' => 'Default Operational Currency',
                'description' => 'Primary transaction currency across campuses (KHR / USD)',
            ],
            [
                'key' => 'exchange_rate_usd_khr',
                'value' => '4100',
                'group' => 'general',
                'type' => 'integer',
                'label' => 'USD to KHR Exchange Peg Rate',
                'description' => 'Standard settlement conversion rate for parent wallets and payouts',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'group' => 'general',
                'type' => 'boolean',
                'label' => 'Platform Maintenance Lockdown',
                'description' => 'Temporarily locks portals for scheduled database upgrades',
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'The SchoolMeal portal is undergoing scheduled maintenance. Services will resume shortly.',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Maintenance Mode Notice Banner',
                'description' => 'Public notice message presented during maintenance windows',
            ],

            // 2. Meal Ordering & Cycles
            [
                'key' => 'daily_order_cutoff_time',
                'value' => '08:00',
                'group' => 'ordering',
                'type' => 'string',
                'label' => 'Daily Order Cutoff Time',
                'description' => 'Time after which meals are batched for preparation and no new cancellations permitted',
            ],
            [
                'key' => 'order_cancellation_window_minutes',
                'value' => '30',
                'group' => 'ordering',
                'type' => 'integer',
                'label' => 'Cancellation Grace Window (Minutes)',
                'description' => 'Grace period allowed for parents to revoke or amend an order prior to cutoff',
            ],
            [
                'key' => 'max_daily_meals_per_student',
                'value' => '3',
                'group' => 'ordering',
                'type' => 'integer',
                'label' => 'Max Daily Meals Per Student',
                'description' => 'Upper ceiling on breakfasts, lunches, and snack orders per pupil per school day',
            ],
            [
                'key' => 'auto_confirm_paid_orders',
                'value' => '1',
                'group' => 'ordering',
                'type' => 'boolean',
                'label' => 'Auto-Confirm Paid Orders',
                'description' => 'Automatically route paid orders straight to kitchen preparation queues',
            ],
            [
                'key' => 'strict_allergy_check',
                'value' => '1',
                'group' => 'ordering',
                'type' => 'boolean',
                'label' => 'Strict Medical Allergy Enforcement',
                'description' => 'Block checkout if dish allergens overlap with student medical nutrition records',
            ],
            [
                'key' => 'allow_weekend_ordering',
                'value' => '0',
                'group' => 'ordering',
                'type' => 'boolean',
                'label' => 'Enable Weekend Meal Ordering',
                'description' => 'Allow meal bookings during Saturday and Sunday extracurricular campus programs',
            ],
            [
                'key' => 'require_parent_consent_for_preorders',
                'value' => '1',
                'group' => 'ordering',
                'type' => 'boolean',
                'label' => 'Require Guardian Pre-Order Consent',
                'description' => 'Enforce parental approval for recurring weekly subscription cycles',
            ],

            // 3. Merchant & Kitchen Governance
            [
                'key' => 'platform_commission_percent',
                'value' => '5.0',
                'group' => 'merchant',
                'type' => 'float',
                'label' => 'Platform Escrow Commission (%)',
                'description' => 'Transaction fee deducted from restaurant merchant gross sales for portal operations',
            ],
            [
                'key' => 'settlement_cycle',
                'value' => 'weekly',
                'group' => 'merchant',
                'type' => 'string',
                'label' => 'Automated Merchant Settlement Cycle',
                'description' => 'Frequency of ACH / Bakong payouts to central restaurant kitchens (daily, weekly, biweekly)',
            ],
            [
                'key' => 'min_merchant_payout_amount',
                'value' => '50000',
                'group' => 'merchant',
                'type' => 'integer',
                'label' => 'Minimum Payout Threshold (KHR)',
                'description' => 'Minimum accumulated sales balance required before escrow transfer release',
            ],
            [
                'key' => 'kitchen_safety_audit_required',
                'value' => '1',
                'group' => 'merchant',
                'type' => 'boolean',
                'label' => 'Enforce Daily Food Safety Checklist',
                'description' => 'Mandate kitchen staff sign-off on hygiene audits prior to firing morning meal shifts',
            ],
            [
                'key' => 'max_active_prep_orders_per_kitchen',
                'value' => '50',
                'group' => 'merchant',
                'type' => 'integer',
                'label' => 'Maximum Concurrent Prep Batch Size',
                'description' => 'Kitchen queue throttler to prevent chef overload during peak delivery windows',
            ],
            [
                'key' => 'prep_lead_time_minutes',
                'value' => '45',
                'group' => 'merchant',
                'type' => 'integer',
                'label' => 'Standard Cooking Lead Time (Minutes)',
                'description' => 'Target cooking and packing window before driver campus dispatch',
            ],

            // 4. Security & Authentication Policy
            [
                'key' => 'enforce_2fa_for_staff',
                'value' => '1',
                'group' => 'security',
                'type' => 'boolean',
                'label' => 'Enforce 2FA for Administrative & Merchant Staff',
                'description' => 'Require SMS OTP or hardware authenticator verification for all staff accounts',
            ],
            [
                'key' => 'session_lifetime_minutes',
                'value' => '120',
                'group' => 'security',
                'type' => 'integer',
                'label' => 'Session Inactivity Lifetime (Minutes)',
                'description' => 'Auto sign-out staff and root administrators after period of idle inactivity',
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'group' => 'security',
                'type' => 'integer',
                'label' => 'Max Failed Login Attempts Before Lockout',
                'description' => 'Threshold of bad passwords before temporary IP and account quarantine',
            ],
            [
                'key' => 'lockout_duration_minutes',
                'value' => '15',
                'group' => 'security',
                'type' => 'integer',
                'label' => 'Security Quarantine Duration (Minutes)',
                'description' => 'Duration of brute-force IP rate-limiting block',
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'group' => 'security',
                'type' => 'integer',
                'label' => 'Minimum Password Length Policy',
                'description' => 'Enforced credential character length across all user personas',
            ],
            [
                'key' => 'audit_log_retention_days',
                'value' => '365',
                'group' => 'security',
                'type' => 'integer',
                'label' => 'Immutable Audit Trail Retention (Days)',
                'description' => 'Minimum lifecycle for financial transactions and security audit records',
            ],

            // 5. Notifications & SMS Telemetry
            [
                'key' => 'sms_gateway_provider',
                'value' => 'Smart Axiata Gateway',
                'group' => 'notifications',
                'type' => 'string',
                'label' => 'National SMS Telecom Gateway Provider',
                'description' => 'Primary telecommunications API provider for meal dispatches and OTP alerts',
            ],
            [
                'key' => 'sms_sender_id',
                'value' => 'SchoolMeal',
                'group' => 'notifications',
                'type' => 'string',
                'label' => 'Verified SMS Sender ID Header',
                'description' => 'Approved telecom alphanumeric sender mask displayed on mobile devices',
            ],
            [
                'key' => 'enable_sms_order_receipts',
                'value' => '1',
                'group' => 'notifications',
                'type' => 'boolean',
                'label' => 'Instant SMS Meal Delivery Receipts',
                'description' => 'Send real-time SMS to parents when student receives lunch at campus counter',
            ],
            [
                'key' => 'enable_daily_parent_digest',
                'value' => '1',
                'group' => 'notifications',
                'type' => 'boolean',
                'label' => 'Daily Consolidated Nutrition Digest',
                'description' => 'Dispatches 6:00 PM summary of child calorie intake and tomorrow meal forecast',
            ],
            [
                'key' => 'low_wallet_balance_alert_khr',
                'value' => '20000',
                'group' => 'notifications',
                'type' => 'integer',
                'label' => 'Low Meal Wallet Balance Trigger (KHR)',
                'description' => 'Alerts parents when student school balance dips below minimum threshold',
            ],
            [
                'key' => 'urgent_emergency_broadcast_enabled',
                'value' => '1',
                'group' => 'notifications',
                'type' => 'boolean',
                'label' => 'Urgent School Emergency Broadcast Channel',
                'description' => 'Allow Super Admin root broadcast of food safety alerts to all connected schools',
            ],
        ];

        foreach ($defaults as $d) {
            static::updateOrCreate(
                ['key' => $d['key']],
                $d
            );
        }
    }
}
