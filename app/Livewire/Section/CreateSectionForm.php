<?php

namespace App\Livewire\Section;

use App\Models\MasterSection;
use App\Models\Scheme;
use Livewire\Component;

class CreateSectionForm extends Component
{

    public $name, $section_name, $section_short_name, $tab_code, $scheme_id, $schemes = [];


    public function mount()
    {
        $this->schemes = Scheme::all();
    }
    protected function rules()
    {
        return [
            'scheme_id' => 'required|exists:schemes,id',
            'section_name' => 'required',
            'section_short_name' => 'required',
            'tab_code' => 'nullable|integer',
        ];
    }

    protected function messages()
    {
        return [
            'scheme_id.required' => 'Please select a scheme.',
            'section_name.required' => 'Section name is required.',
            'section_short_name.required' => 'Section short name is required.',
        ];
    }
    public function save()
    {
        $this->validate();

        MasterSection::create([
            'scheme_id' => $this->scheme_id,
            'section_name' => $this->section_name,
            'section_short_name' => $this->section_short_name,
            'tab_code' => $this->tab_code,
        ]);

        $this->reset([
            'scheme_id',
            'section_name',
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
