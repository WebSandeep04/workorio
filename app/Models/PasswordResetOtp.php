<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetOtp extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean'
    ];

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function isValid()
    {
        return !$this->used && !$this->isExpired();
    }

    public static function generateOtp()
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function createOtp($email)
    {
        // Delete any existing unused OTPs for this email
        self::where('email', $email)
            ->where('used', false)
            ->delete();

        return self::create([
            'email' => $email,
            'otp' => self::generateOtp(),
            'expires_at' => Carbon::now()->addMinutes(10), // OTP expires in 10 minutes
            'used' => false
        ]);
    }
}
