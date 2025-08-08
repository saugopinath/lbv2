<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\BeneficiaryEnclosure;
use App\Models\SchemeAttachedDocMappings;

class EnclosureList extends Component
{
    use WithFileUploads;

    public $doc_lists;
    public $existingDocuments = [];
    public $singleDocument;
    public $currentDocId;
    public $applicationId;

    // Expose current doc rules for display in Blade
    public $currentDocMaxSize = '';
    public $currentDocExtensions = '';

    public function mount($applicationId = null)
    {
        $this->applicationId = $applicationId;

        $this->doc_lists = SchemeAttachedDocMappings::with('codemaster')->get();

        if ($applicationId) {
            $app = DraftBeneficiaryPersonal::with('documents')->where('application_id', $applicationId)->first();
            if ($app) {
                foreach ($app->documents as $doc) {
                    $this->existingDocuments[$doc->document_type] = $doc;
                }
            }
        }
    }

    // Called from Alpine to set current doc info
    public function setCurrentDoc($docTypeId)
    {
        $this->currentDocId = $docTypeId;

        $doc = $this->doc_lists->firstWhere('doc_type_id', $docTypeId);

        if ($doc) {
            $this->currentDocMaxSize = $doc->max_file_size;
            $this->currentDocExtensions = $doc->extension_type;
        } else {
            $this->currentDocMaxSize = '';
            $this->currentDocExtensions = '';
        }
    }

    protected function rules()
    {
        if (!$this->currentDocId) {
            return [];
        }

        $doc = $this->doc_lists->firstWhere('doc_type_id', $this->currentDocId);

        if (!$doc) {
            return [];
        }

        preg_match('/(\d+)/', $doc->max_file_size, $matches);
        $maxSizeKB = isset($matches[1]) ? (int) $matches[1] : 1024;

        $extensionsArray = array_map('trim', explode(',', strtolower($doc->extension_type)));
        $mimesRule = implode(',', $extensionsArray);

        $requiredRule = $doc->is_required ? 'required' : 'nullable';

        return [
            'singleDocument' => "$requiredRule|file|mimes:$mimesRule|max:$maxSizeKB",
        ];
    }

    public function saveSingleDocument()
    {
        $this->validate();

        if (!$this->singleDocument) {
            session()->flash('error', 'No file selected.');
            return;
        }

        $base64 = base64_encode(file_get_contents($this->singleDocument->getRealPath()));

        BeneficiaryEnclosure::create([
            'application_id' => $this->applicationId ?? 0,
            'attched_document' => $base64,
            'ip_address' => request()->ip(),
            'document_extension' => strtolower($this->singleDocument->getClientOriginalExtension()),
            'document_mime_type' => $this->singleDocument->getMimeType(),
            'document_type' => $this->currentDocId,
            'created_by' => Auth::id(),
        ]);

        $this->singleDocument = null;
        $this->currentDocId = null;
        $this->currentDocMaxSize = '';
        $this->currentDocExtensions = '';

        if ($this->applicationId) {
            $app = DraftBeneficiaryPersonal::with('documents')->where('application_id', $this->applicationId)->first();
            if ($app) {
                $this->existingDocuments = [];
                foreach ($app->documents as $doc) {
                    $this->existingDocuments[$doc->document_type] = $doc;
                }
            }
        }

        session()->flash('success', 'Document uploaded successfully.');
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
