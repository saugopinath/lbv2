<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BeneficiaryEnclosure;
use App\Models\BeneficiaryTemEnclosure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DocumentViewController extends Controller
{
    public function view($id)
    {
        // Search in pension.beneficiary_documents or beneficiary_tem_enclosures
        $document = BeneficiaryEnclosure::find($id);
        if (!$document) {
            $document = BeneficiaryTemEnclosure::find($id);
        }

        if (!$document) {
            abort(404, 'Document not found');
        }

        $attVal = $document->attched_document;

        if (Str::isUuid($attVal)) {
            try {
                $response = Http::withHeaders([
                    'app_id' => config('services.doc_storage.app_id'),
                    'client_secret' => config('services.doc_storage.client_secret'),
                ])->get(config('services.doc_storage.base_url') . "/api/Documents/{$attVal}/download");

                if ($response->successful()) {
                    return response($response->body())
                        ->header('Content-Type', $document->document_mime_type ?: 'application/octet-stream');
                }
            } catch (\Exception $e) {
                // fallback to abort
            }
            abort(500, 'Failed to fetch document from storage');
        }

        // Decode base64 for legacy files
        $decoded = base64_decode($attVal);
        return response($decoded)
            ->header('Content-Type', $document->document_mime_type ?: 'application/octet-stream');
    }
}
