<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\BeneficiaryEnclosure;
use Illuminate\Support\Facades\Auth;
use App\Models\BeneficiaryTemEnclosure;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\SchemeAttachedDocMappings;

class EnclosureList extends Component
{
    use WithFileUploads;
    public $doc_lists, $scheme_id, $tabCode;
    public $existingDocuments = [];
    public $singleDocument;
    public $currentDocId;
    public $application_id;
    public $currentDocMaxSize = '';
    public $currentDocExtensions = '';
    public $mode, $is_page, $form_preview;
    public $doc_type_id_array_list = [];
    public $doc_type_id_array = [];
    public $showErrors = false;
    public $enclosureSource = null;
    public $showUploadModal = false;

    public function mount($scheme_id, $application_id = null, $is_page = null, $doc_type_id_array_list = [], $doc_type_id_array = [], $enclosureSource = null, $form_preview = null, $tabCode = null)
    {
        $this->scheme_id = $scheme_id;
        $this->application_id = $application_id;
        // dd($this->application_id);
        $this->is_page        = $is_page;
        $this->enclosureSource = $enclosureSource;
        $this->form_preview = $form_preview;

        $this->tabCode = $tabCode;
        // dd($this->enclosureSource);
        $this->doc_type_id_array_list = $doc_type_id_array_list;
        $this->doc_type_id_array      = $doc_type_id_array;

        $conditions = [
            ['scheme_id', '=', $this->scheme_id],
        ];
        if (!empty($this->tabCode)) {
            $conditions[] = ['tab_code', '=', $this->tabCode];
        }

        if (!empty($this->doc_type_id_array)) {
            $this->doc_lists = SchemeAttachedDocMappings::with('codemaster')
                ->whereIn('doc_type_id', $this->doc_type_id_array)
                ->get();

            if ($application_id) {
                $app = BeneficiaryEnclosure::where('application_id', $application_id)
                    ->whereIn('document_type', $this->doc_type_id_array)
                    ->get();

                foreach ($app as $doc) {
                    $this->existingDocuments[$doc->document_type] = $doc;
                }
            }
        } else {
            // dd($this->scheme_id);
            if (!empty($this->doc_type_id_array_list)) {

                if ($this->enclosureSource == 5) {
                    $app = BeneficiaryTemEnclosure::where('application_id', $application_id)
                        ->whereIn('document_type', $this->doc_type_id_array_list)
                        ->get();
                    // dd($app);
                } else {
                    $app = BeneficiaryEnclosure::where('application_id', $application_id)
                        ->whereIn('document_type', $this->doc_type_id_array_list)
                        ->get();
                }


                foreach ($app as $doc) {
                    $this->existingDocuments[$doc->document_type] = $doc;
                }

                if ($is_page == 1) {
                    $uploadedTypes = array_keys($this->existingDocuments);
                    $this->doc_lists = SchemeAttachedDocMappings::with('codemaster')
                        ->whereIn('doc_type_id', $uploadedTypes)
                        ->get();
                } else {
                    $this->doc_lists = SchemeAttachedDocMappings::with('codemaster')
                        ->whereIn('doc_type_id', $this->doc_type_id_array_list)
                        ->get();
                }
            } else {

                $this->doc_lists = SchemeAttachedDocMappings::with('codemaster')->where($conditions)->get();

                if ($application_id) {
                    $app = BeneficiaryEnclosure::where('application_id', $application_id)->get();

                    foreach ($app as $doc) {
                        $this->existingDocuments[$doc->document_type] = $doc;
                    }

                    if ($is_page == 1) {
                        $uploadedTypes = array_keys($this->existingDocuments);
                        $this->doc_lists = $this->doc_lists->whereIn('doc_type_id', $uploadedTypes);
                    }
                }
            }
        }
    }
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

        $this->resetErrorBag('singleDocument');
    }

    protected function rules()
    {
        if (!$this->currentDocId) {
            return ['singleDocument' => 'nullable|file'];
        }

        $doc = $this->doc_lists->firstWhere('doc_type_id', $this->currentDocId);
        if (!$doc) {
            return ['singleDocument' => 'nullable|file'];
        }

        preg_match('/(\d+)/', $doc->max_file_size, $matches);
        $maxSizeKB = isset($matches[1]) ? (int) $matches[1] : 1024;

        $extensionsArray = array_map('trim', explode(',', strtolower($doc->extension_type)));
        $mimesRule = implode(',', $extensionsArray);

        $requiredRule = $doc->is_required ? 'required' : 'nullable';

        // Set properties early
        $this->currentDocMaxSize = $maxSizeKB . ' KB';
        $this->currentDocExtensions = strtoupper($mimesRule);

        return [
            'singleDocument' => "$requiredRule|file|mimes:$mimesRule|max:$maxSizeKB",
        ];
    }

    protected function messages()
    {
        $doc = $this->doc_lists->firstWhere('doc_type_id', $this->currentDocId);
        $docName = $doc?->codemaster?->name ?? 'Document';
        $extensions = $this->currentDocExtensions ?: 'JPG, PNG, PDF';
        $maxSize    = $this->currentDocMaxSize ?: '1024 KB';

        return [
            'singleDocument.required' => "{$docName} is required.",
            'singleDocument.mimes'    => "{$docName} must be of type: {$extensions}.",
            'singleDocument.max'      => "{$docName} must not be greater than {$maxSize}.",
        ];
    }

    public function resetSingleDocumentErrors()
    {
        $this->resetErrorBag('singleDocument');
    }
    protected function enclosureModel()
    {
        return $this->enclosureSource === '5'
            ? new BeneficiaryTemEnclosure
            : new BeneficiaryEnclosure;
    }

    public function saveSingleDocument()
    {
        if (!$this->singleDocument) {
            $doc = $this->doc_lists->firstWhere('doc_type_id', $this->currentDocId);
            $docName = $doc?->codemaster?->name ?? 'Document';
            $this->addError('singleDocument', "{$docName} is required.");
            return;
        }

        $this->validate();

        $base64 = base64_encode(file_get_contents($this->singleDocument->getRealPath()));

        $model = $this->enclosureModel();
        // dd($model);
        $existingDoc = $model::where('application_id', $this->application_id)
            ->where('document_type', $this->currentDocId)
            ->first();
        // dd($existingDoc);
        if ($existingDoc) {
            $existingDoc->update([
                'attched_document' => $base64,
                'ip_address' => request()->ip(),
                'document_extension' => strtolower($this->singleDocument->getClientOriginalExtension()),
                'document_mime_type' => $this->singleDocument->getMimeType(),
                'created_by' => Auth::id(),
            ]);
        } else {
            $model::create([
                'application_id' => $this->application_id,
                'attched_document' => $base64,
                'ip_address' => request()->ip(),
                'document_extension' => strtolower($this->singleDocument->getClientOriginalExtension()),
                'document_mime_type' => $this->singleDocument->getMimeType(),
                'document_type' => $this->currentDocId,
                'created_by' => Auth::id(),
            ]);
            // dd($is_upload);
        }

        $docId = $this->currentDocId;

        $this->singleDocument = null;
        $this->currentDocId = null;
        $this->currentDocMaxSize = '';
        $this->currentDocExtensions = '';
        $this->showUploadModal = false;
        if ($this->application_id) {
            if ($this->enclosureSource === '5') {
                // dd( 'here');
                $docs = BeneficiaryTemEnclosure::where('application_id', $this->application_id)
                    ->whereIn('document_type', $this->doc_type_id_array_list)
                    ->get();
                $this->existingDocuments = [];
                foreach ($docs as $doc) {
                    $this->existingDocuments[$doc->document_type] = $doc;
                }
            } else {
                $app = DraftBeneficiaryPersonal::with('documents')->where('application_id', $this->application_id)->first();
                if ($app) {
                    $this->existingDocuments = [];
                    foreach ($app->documents as $doc) {
                        $this->existingDocuments[$doc->document_type] = $doc;
                    }
                }
            }
        }

        $this->dispatch('enclosure-saved', message: 'Document uploaded successfully.', docId: $docId);
        $this->dispatch('$refresh');
    }

    public function downloadDocument($id)
    {
        $model = $this->enclosureModel();
        $document = $model::findOrFail($id);
        $decoded = base64_decode($document->attched_document);
        $filename = 'document_' . $document->document_type . '.' . $document->document_extension;

        return response()->streamDownload(function () use ($decoded) {
            echo $decoded;
        }, $filename, [
            'Content-Type' => $document->document_mime_type,
        ]);
    }

    public function save()
    {
        $this->showErrors = false;
        foreach ($this->doc_lists as $doc) {
            $existing = $this->existingDocuments[$doc->doc_type_id] ?? null;

            if ($doc->is_required && empty($existing)) {
                $this->showErrors = true;
                return;
            }
        }
        $this->dispatch('encList', [
            'message' => "Enclosure lists uploaded successfully for the application id: {$this->application_id}"
        ]);
    }

    public function render()
    {
        return view('livewire.enclosure-list');
    }
}
