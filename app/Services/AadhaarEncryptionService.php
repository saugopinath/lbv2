<?php

namespace App\Services;

use App\Contracts\AadhaarEncryptionServiceInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AadhaarEncryptionService implements AadhaarEncryptionServiceInterface
{
    public function __construct(
        private readonly string $url,
        private readonly string $apiKey,
        private readonly string $environment
    ) {}

    public function generateEncryptedAadhaar(string $aadhaarNumber): string
    {
        if ($this->environment !== 'production' || empty($this->url) || empty($this->apiKey)) {
            return md5($aadhaarNumber); // Mock for local/testing
        }

        return $this->sendRequest('/tokenize', ['aadhaar' => $aadhaarNumber], 'reference_key', 'Tokenization');
    }

    public function detokenize(string $referenceKey): string
    {
        if ($this->environment !== 'production' || empty($this->url) || empty($this->apiKey)) {
            return $referenceKey; // Mock for local/testing
        }

        return $this->sendRequest('/detokenize', ['reference_key' => $referenceKey], 'aadhaar', 'Detokenization');
    }

    private function sendRequest(string $endpoint, array $payload, string $responseKey, string $action): string
    {
        try {
            $response = $this->client()->post($endpoint, $payload);

            if ($response->successful() && $response->has($responseKey)) {
                return (string) $response->json($responseKey);
            }

            throw new Exception("Aadhaar {$action} API returned an error status.");
        } catch (Exception $e) {
            Log::critical("Aadhaar {$action} Failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->url)
            ->withToken($this->apiKey)
            ->acceptJson();
    }
}
