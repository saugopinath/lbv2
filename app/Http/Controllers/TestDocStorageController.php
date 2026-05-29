<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestDocStorageController extends Controller
{
    public function index(Request $request)
    {
        $baseUrl = $request->input('base_url', config('services.doc_storage.base_url'));
        $appId = $request->input('app_id', config('services.doc_storage.app_id'));
        $clientSecret = $request->input('client_secret', config('services.doc_storage.client_secret'));

        return view('test-doc-storage', compact('baseUrl', 'appId', 'clientSecret'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'base_url' => 'required|url',
            'app_id' => 'required',
            'client_secret' => 'required',
            'created_by' => 'required|integer',
        ]);

        $baseUrl = $request->input('base_url');
        $appId = $request->input('app_id');
        $clientSecret = $request->input('client_secret');
        $createdBy = $request->input('created_by');
        $file = $request->file('file');

        $apiUrl = rtrim($baseUrl, '/') . '/api/Documents/upload';
        $headers = [
            'app_id' => $appId,
            'client_secret' => $clientSecret,
        ];

        $requestLog = [
            'url' => $apiUrl,
            'headers' => $headers,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize() . ' bytes',
            'created_by' => $createdBy,
        ];

        try {
            $response = Http::withHeaders($headers)
                ->attach('File', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post($apiUrl, [
                    'CreatedBy' => $createdBy,
                ]);

            $status = $response->status();
            $body = $response->json();

            if ($response->successful()) {
                return redirect()->route('test-doc-storage.index', [
                    'base_url' => $baseUrl,
                    'app_id' => $appId,
                    'client_secret' => $clientSecret,
                ])->with([
                    'success' => 'Document uploaded successfully!',
                    'documentId' => $body['result']['documentId'] ?? null,
                    'upload_response' => $body,
                    'request_log' => $requestLog,
                    'response_status' => $status,
                ]);
            }

            return redirect()->back()->withInput()->with([
                'error' => 'Upload failed. HTTP Status: ' . $status,
                'upload_response' => $body ?: $response->body(),
                'request_log' => $requestLog,
                'response_status' => $status,
            ]);

        } catch (\Exception $e) {
            Log::error('Doc Storage Test Upload Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with([
                'error' => 'Connection Error: ' . $e->getMessage(),
                'request_log' => $requestLog,
            ]);
        }
    }

    public function download(Request $request, $id)
    {
        $baseUrl = $request->input('base_url', config('services.doc_storage.base_url'));
        $appId = $request->input('app_id', config('services.doc_storage.app_id'));
        $clientSecret = $request->input('client_secret', config('services.doc_storage.client_secret'));

        $apiUrl = rtrim($baseUrl, '/') . "/api/Documents/{$id}/download";
        $headers = [
            'app_id' => $appId,
            'client_secret' => $clientSecret,
        ];

        try {
            $response = Http::withHeaders($headers)->get($apiUrl);

            if ($response->successful()) {
                $contentType = $response->header('Content-Type') ?: 'application/octet-stream';
                $contentDisposition = $response->header('Content-Disposition');
                
                $filename = 'downloaded_doc_' . substr($id, 0, 8);
                if ($contentDisposition && preg_match('/filename="?([^"]+)"?/', $contentDisposition, $matches)) {
                    $filename = $matches[1];
                }

                return response()->streamDownload(function () use ($response) {
                    echo $response->body();
                }, $filename, [
                    'Content-Type' => $contentType,
                ]);
            }

            return redirect()->route('test-doc-storage.index', [
                'base_url' => $baseUrl,
                'app_id' => $appId,
                'client_secret' => $clientSecret,
            ])->with([
                'error' => 'Download failed. HTTP Status: ' . $response->status(),
                'response_status' => $response->status(),
                'upload_response' => $response->json() ?: $response->body(),
            ]);

        } catch (\Exception $e) {
            return redirect()->route('test-doc-storage.index', [
                'base_url' => $baseUrl,
                'app_id' => $appId,
                'client_secret' => $clientSecret,
            ])->with([
                'error' => 'Download connection error: ' . $e->getMessage(),
            ]);
        }
    }

    public function delete(Request $request, $id)
    {
        $baseUrl = $request->input('base_url', config('services.doc_storage.base_url'));
        $appId = $request->input('app_id', config('services.doc_storage.app_id'));
        $clientSecret = $request->input('client_secret', config('services.doc_storage.client_secret'));

        $apiUrl = rtrim($baseUrl, '/') . '/api/Documents/DeleteDocument';
        $headers = [
            'app_id' => $appId,
            'client_secret' => $clientSecret,
        ];

        $requestLog = [
            'url' => $apiUrl,
            'headers' => $headers,
            'method' => 'DELETE',
            'body' => ['documentId' => $id],
        ];

        try {
            $response = Http::withHeaders($headers)->delete($apiUrl, [
                'documentId' => $id,
            ]);

            $status = $response->status();
            $body = $response->json();

            if ($response->successful()) {
                return redirect()->route('test-doc-storage.index', [
                    'base_url' => $baseUrl,
                    'app_id' => $appId,
                    'client_secret' => $clientSecret,
                ])->with([
                    'success' => 'Document deleted successfully!',
                    'upload_response' => $body,
                    'request_log' => $requestLog,
                    'response_status' => $status,
                ]);
            }

            return redirect()->route('test-doc-storage.index', [
                'base_url' => $baseUrl,
                'app_id' => $appId,
                'client_secret' => $clientSecret,
            ])->with([
                'error' => 'Delete failed. HTTP Status: ' . $status,
                'upload_response' => $body ?: $response->body(),
                'request_log' => $requestLog,
                'response_status' => $status,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('test-doc-storage.index', [
                'base_url' => $baseUrl,
                'app_id' => $appId,
                'client_secret' => $clientSecret,
            ])->with([
                'error' => 'Delete connection error: ' . $e->getMessage(),
                'request_log' => $requestLog,
            ]);
        }
    }
}
