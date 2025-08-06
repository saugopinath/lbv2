<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\BeneficiaryEnclosure;
use App\Models\SchemeAttachedDocMappings;
use App\Models\MasterMimeType;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EnclosureList extends Component
{
    use WithFileUploads;

    public $doc_lists;
    public $documents = [];
    public $existingDocuments = [];
    public $mode;
    public $id;

    public function mount($mode = null, $id = null)
    {
        $this->mode = $mode;
        $this->id = $id;
        $this->doc_lists = SchemeAttachedDocMappings::with('codemaster')->get();

        if ($id !== null) {
            $app = DraftBeneficiaryPersonal::with('documents')->where('application_id', $id)->first();
            if ($app) {
                foreach ($app->documents as $doc) {
                    $this->existingDocuments[$doc->document_type] = $doc;
                }
            }
        }
    }

    protected function rules($docId)
    {
        $doc = $this->doc_lists->where('codemaster.id', $docId)->first();

        if (!$doc) {
            return [];
        }

        $maxSize = (int) filter_var($doc->max_file_size, FILTER_SANITIZE_NUMBER_INT);

        $extensions = array_map('strtolower', array_filter(
            explode(',', str_replace([';', '|', ' '], ',', $doc->extension_type))
        ));

        $mimeTypes = MasterMimeType::whereIn('extension_type', $extensions)->pluck('mime_type')->toArray();
        $requiredRule = $doc->is_required ? 'required' : 'nullable';

        return [
            "documents.$docId" => "$requiredRule|file|mimes:" . implode(',', $extensions) . "|mimetypes:" . implode(',', $mimeTypes) . "|max:$maxSize",
        ];
    }

    public function saveSingleDocument($docId)
    {
        $this->validate($this->rules($docId));

        $file = data_get($this->documents, $docId);

        if ($file instanceof TemporaryUploadedFile) {
            $base64 = base64_encode(file_get_contents($file->getRealPath()));

            BeneficiaryEnclosure::create([
                'application_id'        => $this->id ?? 5,
                'attched_document'      => $base64,
                'ip_address'            => request()->ip(),
                'document_extension'    => strtolower($file->getClientOriginalExtension()),
                'document_mime_type'    => $file->getMimeType(),
                'document_type'         => $docId,
                'created_by'            => Auth::id(),
            ]);

            // Clear uploaded file
            $this->documents[$docId] = null;

            // Refresh existing documents
            $app = DraftBeneficiaryPersonal::with('documents')->where('application_id', $this->id)->first();
            if ($app) {
                foreach ($app->documents as $doc) {
                    $this->existingDocuments[$doc->document_type] = $doc;
                }
            }
        }
    }

    public function downloadDocument($id)
    {
        $document = BeneficiaryEnclosure::findOrFail($id);
        $decoded = base64_decode($document->attched_document);
        $filename = 'document_' . $document->document_type . '.' . $document->document_extension;

        return response()->streamDownload(function () use ($decoded) {
            echo $decoded;
        }, $filename, [
            'Content-Type' => $document->document_mime_type,
        ]);
    }

    public function render()
    {
        return view('livewire.enclosure-list');
    }
}
