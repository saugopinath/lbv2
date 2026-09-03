<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class DocumentService
{
    public function __construct(
        private readonly string $storageType,
        private readonly string $baseUrl,
        private readonly string $appId,
        private readonly string $clientSecret,
        private readonly string $environment
    ) {}

    private function isDocumentStorageUsable(): bool
    {
        if ($this->storageType == 1) {
            return false;
        }

        if ($this->storageType == 2 && $this->environment === 'production') {
            if (empty($this->baseUrl) || empty($this->appId) || empty($this->clientSecret)) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function uploadDocument($filePath, $fileName, $createdBy = null)
    {
        if ($this->isDocumentStorageUsable()) {
            $fileStream = fopen($filePath, 'r');

            if ($fileStream === false) {
                throw new Exception("Failed to open file stream for path: {$filePath}");
            }
            try {
                
                $response = Http::withHeaders([
                    'app_id' => $this->appId,
                    'client_secret' => $this->clientSecret,
                ])->attach(
                    'File',
                    $fileStream, //When you pass an fopen() resource directly into Laravel’s Http::attach() method, Laravel handles the while loop and chunk reading behind the scenes for you.
                    $fileName
                )->post($this->baseUrl . '/api/Documents/upload', [
                    'CreatedBy' => $createdBy ?? 1
                ]);
            } catch (Exception $e) {
                fclose($fileStream); // Ensure the file stream is closed in case of an exception
                throw new Exception("Upload failed maybe due to network connection timeout or some other reason: " . $e->getMessage());
            }

            if (!$response->successful()) throw new Exception('Upload failed: API connection error (' . $response->status() . ')');

            $data = $response->json();

            if (($data['apiResponseStatus'] ?? null) == 1 || isset($data['result']['documentId'])) {
                return $data;
            }

            throw new Exception('Upload failed: ' . ($data['message'] ?? 'Unknown API error'));
        } else {
            return true;
        }
    }

    /**
     * Download a document from the document storage API.
     *
     * @param string $documentId
     * @return \Illuminate\Http\Client\Response
     */
    public function downloadDocument($documentId)
    {
        $response = Http::withHeaders([
            'app_id' => $this->appId,
            'client_secret' => $this->clientSecret,
        ])->get($this->baseUrl . "/api/Documents/{$documentId}/download");

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['apiResponseStatus']) && $data['apiResponseStatus'] == 1) {
                return $data;
            } else {
                // If it's a duplicate but returns documentId, we could potentially just use it.
                if (isset($data['result']['documentId'])) {
                    return $data;
                }
                throw new Exception('Upload failed: ' . ($data['message'] ?? 'Unknown API error'));
            }
        } else {
            throw new Exception('Upload failed: API connection error (' . $response->status() . ')');
        }
    }

    /**
     * Delete a document from the document storage API.
     *
     * @param string $documentId
     * @return \Illuminate\Http\Client\Response
     */
    public function deleteDocument($documentId)
    {
        return Http::withHeaders([
            'app_id' => $this->appId,
            'client_secret' => $this->clientSecret,
        ])->delete($this->baseUrl . "/api/Documents/DeleteDocument", [
            'documentId' => $documentId
        ]);
    }
}
