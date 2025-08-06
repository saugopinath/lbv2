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
use Illuminate\Support\Facades\Response;

class EnclosureList extends Component
{
    use WithFileUploads;

    public $doc_lists, $documents = [], $existingDocuments = [];
    public $mode, $id;

    public function mount($mode = null, $id = null)
    {
        $this->mode = $mode;
        $this->id = $id;

        $this->doc_lists = SchemeAttachedDocMappings::with('codemaster')->get();

        if ($id !== null) {
            $app = DraftBeneficiaryPersonal::with('documents')->where('application_id', $id)->first();
            foreach ($app->documents as $doc) {
                $this->existingDocuments[$doc->document_type] = $doc;
            }
        }
    }

    protected function rules()
    {
        $rules = [];

        foreach ($this->doc_lists as $doc) {
            $field = $doc->codemaster->id;
            $maxSize = (int) filter_var($doc->max_file_size, FILTER_SANITIZE_NUMBER_INT);
            $extensions = array_map('strtolower', array_filter(
                explode(',', str_replace([';', '|', ' '], ',', $doc->extension_type))
            ));
            $mimeTypes = MasterMimeType::whereIn('extension_type', $extensions)->pluck('mime_type')->toArray();

            $required = ($doc->is_required && !isset($this->existingDocuments[$doc->doc_type_id])) ? 'required' : 'nullable';
            $rules["documents.$field"] = "$required|file|mimes:" . implode(',', $extensions) . "|mimetypes:" . implode(',', $mimeTypes) . "|max:$maxSize";
        }

        return $rules;
    }

    public function save()
    {
        $this->validate();

        foreach ($this->doc_lists as $doc) {
            $field = $doc->codemaster->id;

            if (!empty($this->documents[$field])) {
                $file = $this->documents[$field];
                $base64 = base64_encode(file_get_contents($file->getRealPath()));

                BeneficiaryEnclosure::create([
                    'application_id'        => $this->id,
                    'attched_document'      => $base64,
                    'ip_address'            => request()->ip(),
                    'document_extension'    => strtolower($file->getClientOriginalExtension()),
                    'document_mime_type'    => $file->getMimeType(),
                    'document_type'         => $doc->doc_type_id,
                    'created_by'            => Auth::id(),
                ]);
            }
        }

        session()->flash('success', 'Documents uploaded successfully.');
    }

    public function downloadDocument($id)
    {
        $document = BeneficiaryEnclosure::findOrFail($id);

        $decoded = base64_decode($document->attched_document);
        $filename = 'document_' . $document->id . '.' . $document->document_extension;

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
