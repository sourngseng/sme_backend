<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class OtpRequest extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'phone_or_email',
        'otp_hash',
        'expires_at',
        'attempts',
        'verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    /**
     * Check if the OTP request has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the OTP has already been verified.
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Check if attempt count exceeded threshold.
     */
    public function hasExceededMaxAttempts(int $max = 5): bool
    {
        return $this->attempts >= $max;
    }

    /**
     * Verify the provided plain OTP string.
     */
    public function verify(string $plainOtp): bool
    {
        if ($this->isExpired() || $this->isVerified() || $this->hasExceededMaxAttempts()) {
            return false;
        }

        if (Hash::check($plainOtp, $this->otp_hash)) {
            $this->update(['verified_at' => now()]);
            return true;
        }

        $this->increment('attempts');
        return false;
    }
}
