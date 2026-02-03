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
    protected $listeners = [
        'check-documents-before-next' => 'validateBeforeNext',
    ];


    public function mount($scheme_id, $application_id = null, $is_page = null, $doc_type_id_array_list = [], $doc_type_id_array = [], $enclosureSource = null, $form_preview = null, $tabCode = null)
    {
        $this->scheme_id = $scheme_id;
        $this->application_id = $application_id;
        // dd($this->application_id);
        $this->is_page = $is_page;
        $this->enclosureSource = $enclosureSource;
        $this->form_preview = $form_preview;

        $this->tabCode = $tabCode;
        // dd( $this->tabCode);
        $this->doc_type_id_array_list = $doc_type_id_array_list;
        $this->doc_type_id_array = $doc_type_id_array;
        $this->loadDocumentList();
        $this->loadExistingDocuments();
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
    /* ================= HELPERS ================= */

    protected function enclosureModel()
    {
        return $this->enclosureSource === '5'
            ? BeneficiaryTemEnclosure::class
            : BeneficiaryEnclosure::class;
    }

    protected function getDocTypeIds(): array
    {
        if (!empty($this->doc_type_id_array)) {
            return $this->doc_type_id_array;
        }

        if (!empty($this->doc_type_id_array_list)) {
            return $this->doc_type_id_array_list;
        }

        return $this->doc_lists?->pluck('doc_type_id')->toArray() ?? [];
    }

    protected function loadDocumentList(): void
    {
        $query = SchemeAttachedDocMappings::with('codemaster')
            ->where('scheme_id', $this->scheme_id);

        if (!empty($this->tabCode)) {
            $query->where('tab_code', $this->tabCode);
        }

        if (!empty($this->doc_type_id_array)) {
            $query->whereIn('doc_type_id', $this->doc_type_id_array);
        } elseif (!empty($this->doc_type_id_array_list)) {
            $query->whereIn('doc_type_id', $this->doc_type_id_array_list);
        }

        $this->doc_lists = $query->get();
    }

    protected function loadExistingDocuments(): void
    {
        if (!$this->application_id) {
            return;
        }

        $model = $this->enclosureModel();
        $docTypes = $this->getDocTypeIds();

        $docs = $model::where('application_id', $this->application_id)
            ->whereIn('document_type', $docTypes)
            ->get();

        $this->existingDocuments = [];
        foreach ($docs as $doc) {
            $this->existingDocuments[$doc->document_type] = $doc;
        }
    }

    /* ================= FILE SELECT ================= */

    public function setCurrentDoc($docTypeId)
    {
        $this->currentDocId = $docTypeId;

        $doc = $this->doc_lists->firstWhere('doc_type_id', $docTypeId);
        $this->currentDocMaxSize = $doc?->max_file_size ?? '';
        $this->currentDocExtensions = $doc?->extension_type ?? '';

        $this->resetSingleDocumentErrors();
    }

    /** 🔥 THIS METHOD WAS MISSING */
    public function resetSingleDocumentErrors()
    {
        $this->resetErrorBag('singleDocument');
    }

    /* ================= VALIDATION ================= */

    protected function rules()
    {
        if (!$this->currentDocId) {
            return ['singleDocument' => 'nullable|file'];
        }

        $doc = $this->doc_lists->firstWhere('doc_type_id', $this->currentDocId);
        if (!$doc) {
            return ['singleDocument' => 'nullable|file'];
        }

        preg_match('/(\d+)/', $doc->max_file_size, $m);
        $maxKB = $m[1] ?? 1024;

        $extensions = strtolower($doc->extension_type);
        $required = $doc->is_required ? 'required' : 'nullable';

        $this->currentDocMaxSize = $maxKB . ' KB';
        $this->currentDocExtensions = strtoupper($extensions);

        return [
            'singleDocument' => "{$required}|file|mimes:{$extensions}|max:{$maxKB}",
        ];
    }

    protected function messages()
    {
        $doc = $this->doc_lists->firstWhere('doc_type_id', $this->currentDocId);
        $name = $doc?->codemaster?->name ?? 'Document';

        return [
            'singleDocument.required' => "{$name} is required.",
            'singleDocument.mimes' => "{$name} file type not allowed.",
            'singleDocument.max' => "{$name} file size exceeded.",
        ];
    }

    /* ================= SAVE ================= */

    public function saveSingleDocument()
    {
        if (!$this->singleDocument) {
            $this->addError('singleDocument', 'Document is required.');
            return;
        }

        $this->validate();

        $model = $this->enclosureModel();
        $base64 = base64_encode(
            file_get_contents($this->singleDocument->getRealPath())
        );

        $model::updateOrCreate(
            [
                'application_id' => $this->application_id,
                'document_type' => $this->currentDocId,
            ],
            [
                'attched_document' => $base64,
                'document_extension' => strtolower(
                    $this->singleDocument->getClientOriginalExtension()
                ),
                'document_mime_type' => $this->singleDocument->getMimeType(),
                'ip_address' => request()->ip(),
                'created_by' => Auth::id(),
            ]
        );

        $this->reset([
            'singleDocument',
            'currentDocId',
            'currentDocMaxSize',
            'currentDocExtensions',
        ]);

        $this->showUploadModal = false;

        // 🔥 reload documents correctly
        $this->loadExistingDocuments();

        $this->dispatch('enclosure-saved', message: 'Document uploaded successfully.');
        $this->dispatch('$refresh');
    }

    /* ================= VALIDATE BEFORE NEXT ================= */

    public function validateBeforeNext()
    {
        $this->showErrors = false;

        foreach ($this->doc_lists as $doc) {
            $existing = $this->existingDocuments[$doc->doc_type_id] ?? null;

            if ($doc->is_required && empty($existing)) {
                $this->showErrors = true;
                $this->dispatch('document-validation-failed');
                return;
            }
        }

        $this->dispatch('document-validation-passed');
    }

    /* ================= DOWNLOAD ================= */

    public function downloadDocument($id)
    {
        $model = $this->enclosureModel();
        $doc = $model::findOrFail($id);

        return response()->streamDownload(
            fn() => print (base64_decode($doc->attched_document)),
            'document_' . $doc->document_type . '.' . $doc->document_extension,
            ['Content-Type' => $doc->document_mime_type]
        );
    }

    public function render()
    {
        return view('livewire.enclosure-list');
    }
}