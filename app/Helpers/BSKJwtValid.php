<?php

namespace App\Helpers;

use Carbon\Carbon;

class BSKJwtValid
{
    private const SECRET = 'WBCMOPMUBSK';
    private const TTL = 600; // seconds

    public static function is_jwt_valid(?string $jwt): bool
    {
        if (!$jwt) {
            return false;
        }

        /** Remove Bearer prefix if exists */
        if (str_starts_with($jwt, 'Bearer')) {
            $jwt = substr($jwt, 7);
        }

        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return false;
        }

        [$headerB64, $payloadB64, $signatureProvided] = $parts;

        /** Decode payload only for reading */
        $payloadJson = self::base64url_decode($payloadB64);
        $payload = json_decode($payloadJson);

        if (!$payload || empty($payload->iat)) {
            return false;
        }

        /** Expiry check */
        $expiry = Carbon::createFromTimestamp($payload->iat)
            ->addSeconds(self::TTL);

        if (now()->greaterThan($expiry)) {
            return false;
        }
// dd('ok');
        /** Signature check (JWT STANDARD) */
        $expectedSignature = self::base64url_encode(
            hash_hmac(
                'sha256',
                $headerB64 . '.' . $payloadB64,
                self::SECRET,
                true
            )
        );
// dd($expectedSignature, $signatureProvided);
        return hash_equals($expectedSignature, $signatureProvided);
    }

    private static function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64url_decode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }
}
