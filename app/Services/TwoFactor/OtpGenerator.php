<?php
namespace App\Services\TwoFactor;

use App\Contracts\TwoFactorGeneratorInterface;

class OtpGenerator implements TwoFactorGeneratorInterface
{
    public function generate(): string
    {
        if (env('APP_ENV') == 'local' || env('APP_ENV') == 'staging') {
            return '123456';
        }
        return (string) rand(111111, 999999);
    }

    public function getLabel(): string
    {
        return 'OTP';
    }

    public function requiresSms(): bool
    {
        return true;
    }

    public function getDisplayCode(string $secretOrOtp): string
    {
        return $secretOrOtp;
    }

    public function validate(string $secretOrOtp, string $input): bool
    {
        return $secretOrOtp === $input;
    }

    public function getExpirationMinutes(): int
    {
        return config('auth.otp_expire_minutes', 2);
    }
}
