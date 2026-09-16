<?php
namespace App\Services\TwoFactor;

use App\Contracts\TwoFactorGeneratorInterface;
use Google2FA;

class TotpGenerator implements TwoFactorGeneratorInterface
{
    public function generate(): string
    {
       /* if (env('APP_ENV') == 'local' || env('APP_ENV') == 'staging') {
            return '123456';
        }*/
        return Google2FA::generateSecretKey();
    }

    public function getLabel(): string
    {
        return 'TOTP';
    }

    public function requiresSms(): bool
    {
        return true;
    }

    public function getDisplayCode(string $secretOrOtp): string
    {
        /*
        if (env('APP_ENV') == 'local' || env('APP_ENV') == 'staging') {
            return '123456';
        }
        */
        return Google2FA::getCurrentOtp($secretOrOtp);
    }

    public function validate(string $secretOrOtp, string $input): bool
    {
        /*
        if (env('APP_ENV') == 'local' || env('APP_ENV') == 'staging') {
            return $input === '123456';
        }
            */
        // Use a window of 2 periods to allow for significant clock skew between the server and the phone
        return Google2FA::verifyKey($secretOrOtp, $input, 2);
    }

    public function getExpirationMinutes(): int
    {
        return config('google2fa.lifetime', 2);
    }
}
