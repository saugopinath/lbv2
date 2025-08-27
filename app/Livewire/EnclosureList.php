<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\BeneficiaryEnclosure;
use App\Models\SchemeAttachedDocMappings;
use Illuminate\Support\Facades\DB;

class EnclosureList extends Component
{
    use WithFileUploads;
    public $doc_lists;
    public $existingDocuments = [];
    public $singleDocument;
    public $currentDocId;
    public $application_id;
    public $currentDocMaxSize = '';
    public $currentDocExtensions = '';
    public $mode, $is_page;
    public $doc_type_id_array_list = [];
    public $doc_type_id_array = [];
    public $showErrors = false;
    public function mount($mode = null, $application_id = null, $is_page = null, $doc_type_id_array_list = [], $doc_type_id_array = [])
    {
        $this->application_id = $application_id;
        $this->is_page        = $is_page;

        $this->doc_type_id_array_list = $doc_type_id_array_list;
        $this->doc_type_id_array      = $doc_type_id_array;

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
            if (!empty($this->doc_type_id_array_list)) {
                $app = BeneficiaryEnclosure::where('application_id', $application_id)
                    ->whereIn('document_type', $this->doc_type_id_array_list)
                    ->get();

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
                $this->doc_lists = SchemeAttachedDocMappings::with('codemaster')->get();

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
        $this->currentDocMaxSize = $doc->max_file_size;
        $this->currentDocExtensions = $doc->extension_type;
    }
    protected function rules()
    {
        $doc = $this->doc_lists->firstWhere('doc_type_id', $this->currentDocId);
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
        $base64 = base64_encode(file_get_contents($this->singleDocument->getRealPath()));
        $existingDoc = BeneficiaryEnclosure::where('application_id', $this->application_id)
            ->where('document_type', $this->currentDocId)
            ->first();
        DB::beginTransaction();
        try {
            if ($existingDoc) {
                $existingDoc->update([
                    'attched_document' => $base64,
                    'ip_address' => request()->ip(),
                    'document_extension' => strtolower($this->singleDocument->getClientOriginalExtension()),
                    'document_mime_type' => $this->singleDocument->getMimeType(),
                    'created_by' => 1,
                ]);
            } else {
                BeneficiaryEnclosure::create([
                    'application_id' => $this->application_id,
                    'attched_document' => $base64,
                    'ip_address' => request()->ip(),
                    'document_extension' => strtolower($this->singleDocument->getClientOriginalExtension()),
                    'document_mime_type' => $this->singleDocument->getMimeType(),
                    'document_type' => $this->currentDocId,
                    'created_by' => 1,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        $this->singleDocument = null;
        $this->currentDocId = null;
        $this->currentDocMaxSize = '';
        $this->currentDocExtensions = '';
        if ($this->application_id) {
            $app = DraftBeneficiaryPersonal::with('documents')->where('application_id', $this->application_id)->first();
            if ($app) {
                $this->existingDocuments = [];
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
    public function save()
    {
        $this->showErrors = false;
        foreach ($this->doc_lists as $doc) {
            $existing = $this->existingDocuments[$doc->doc_type_id] ?? null;

            if ($doc->is_required && empty($existing)) {
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
