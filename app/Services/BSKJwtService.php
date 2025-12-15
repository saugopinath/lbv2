<?php

namespace App\Services;

use Carbon\Carbon;

class BSKJwtService
{
    protected static string $secret = 'WBCMOPMUBSK';

    public static function validate(): ?object
    {
        $jwt = request()->bearerToken() ?? request('token');

        if (!$jwt) {
            return null;
        }

        $tokenParts = explode('.', $jwt);

        if (count($tokenParts) !== 3) {
            return null;
        }

        [$headerB64, $payloadB64, $signatureProvided] = $tokenParts;

        $header  = self::base64UrlDecode($headerB64);
        $payload = self::base64UrlDecode($payloadB64);

        if (!$payload) {
            return null;
        }

        $payloadObj = json_decode($payload);

        // Token expiry check (iat + 10 minutes)
        if (!isset($payloadObj->iat)) {
            return null;
        }

        $expiresAt = Carbon::createFromTimestamp($payloadObj->iat)->addMinutes(10);

        if (now()->greaterThan($expiresAt)) {
            return null;
        }

        // Signature verify
        $signature = hash_hmac(
            'sha256',
            $headerB64 . '.' . $payloadB64,
            self::$secret,
            true
        );

        $signatureEncoded = self::base64UrlEncode($signature);

        if (!hash_equals($signatureEncoded, $signatureProvided)) {
            return null;
        }

        return $payloadObj;
    }

    protected static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected static function base64UrlDecode(string $data): string|false
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
