<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AadhaarEncryptionService
{
    public static function generateEncryptedAadhaar(string $aadhaarNumber): string
    {
        $url = config('services.adv.url') ?? '';
        $apiKey = config('services.adv.key') ?? '';
        $environement = config('app.env');

        // Implementation for generating encrypted Aadhaar
        if ($environement === 'production' && !empty($url) && !empty($apiKey)) {
            try {
                $response = Http::withToken($apiKey)
                    ->acceptJson()
                    ->post("{$url}/tokenize", [
                        'aadhaar' => $aadhaarNumber
                    ]);

                if ($response->successful()) {
                    return $response->json('reference_key');
                }

                throw new Exception('Aadhaar tokenization API returned an error status.');
            } catch (Exception $e) {
                Log::critical('Aadhaar Tokenization Failed: ' . $e->getMessage());
                throw $e;
            }
        }
        return md5($aadhaarNumber); // For Local
    }

    // /**
    //  * Exchange a Reference Key back to a raw Aadhaar number (for verified users/audits).
    //  */
    // public function detokenize(string $referenceKey): string
    // {
    //     try {
    //         $response = Http::withToken($apiKey)
    //             ->acceptJson()
    //             ->post("{$url}/detokenize", [
    //                 'reference_key' => $referenceKey
    //             ]);

    //         if ($response->successful()) {
    //             return $response->json('aadhaar');
    //         }

    //         throw new Exception('Aadhaar detokenization API returned an error status.');
    //     } catch (Exception $e) {
    //         Log::critical('Aadhaar Detokenization Failed: ' . $e->getMessage());
    //         throw $e;
    //     }
    // }
}
