<?php

namespace App\Livewire\Section;

use App\Models\MasterSection;
use App\Models\MasterTab;
use App\Models\Scheme;
use App\Models\SectionLevelMaster;
use Illuminate\Contracts\Encryption\DecryptException;
use Livewire\Component;

class CreateSectionForm extends Component
{

    public $name, $section_name, $tab_code, $scheme_id, $schemes = [], $tabs, $lock = false;
    public $section_level_name,$section_short_name;  

    public function mount($data = null)
    {
        $this->schemes = Scheme::all();
        $this->tabs = MasterTab::all();

        if ($data) {
            try {
                $this->scheme_id = $data['scheme_id'];
                $this->tab_code = $data['tab_code'];
                if (filled($this->scheme_id) && filled($this->tab_code)) {
                    $this->lock = true;
                }
            } catch (DecryptException $e) {
                abort(403, 'Invalid scheme reference');
            }
        }
    }
    protected function rules()
    {
        return [
            'scheme_id' => 'required|exists:schemes,id',
            'section_short_name' => 'required',
            'tab_code' => 'required',
        ];
    }

    protected function messages()
    {
        return [
            'scheme_id.required' => 'Please select a scheme.',
            'section_short_name.required' => 'Section short name is required.',
        ];
    }
    public function updatedSectionLevelName($value)
    {
        $this->section_short_name = strtolower(
            preg_replace('/[^a-zA-Z0-9]+/', '_', trim($value))
        );
    }
    public function save()
    {
        $this->validate();

        SectionLevelMaster::create([
            'scheme_id' => $this->scheme_id,
            'section_level_name' => $this->section_level_name,
            'section_level_short_name' => $this->section_short_name,
            'section_level_code' => 0,
            'tab_code' => $this->tab_code,
        ]);

        $this->reset([
            'scheme_id',
            'section_short_name',
            'tab_code',
        ]);
        $this->dispatch('hideLoader');
        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Section created successfully!'
        ]);
        $this->dispatch('close-modal');
    }
    public function cancel()
    {
        $this->reset(['name']);
        $this->dispatch('close-modal');
    }

    // public function getParentsProperty()
    // {
    //     return Permission::whereNull('parent_id')->get();
    // }
    public function render()
    {
        return view('livewire.section.create-section-form');
    }
}
