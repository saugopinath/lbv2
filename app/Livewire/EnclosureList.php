<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\BeneficiaryEnclosure;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Models\SchemeAttachedDocMappings;

class EnclosureList extends Component
{
    use WithFileUploads;

    public $doc_lists = [];
    public $mode;
    public $documents = [];

    public function mount($mode = null)
    {
        $this->mode = $mode;
        $this->doc_lists = SchemeAttachedDocMappings::with('codemaster')->get();
    }

    public function save()
    {
        $application = DraftBeneficiaryPersonal::first();

        foreach ($this->doc_lists as $doc) {
            $field = $doc->short_name;

            if (!empty($this->documents[$field])) {
                $file = $this->documents[$field];
                $base64 = base64_encode(file_get_contents($file->getRealPath()));

                BeneficiaryEnclosure::create([
                    'application_id'        => $application->application_id,
                    'attched_document'      => $base64,
                    'ip_address'            => request()->ip(),
                    'document_extension'    => $file->getClientOriginalExtension(),
                    'document_mime_type'    => $file->getMimeType(),
                    'document_type'         => $doc->id,
                    'created_by'            => Auth::id(),
                ]);

                if ($file instanceof TemporaryUploadedFile && file_exists($file->getRealPath())) {
                    @unlink($file->getRealPath());
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.enclosure-list');
    }
}
