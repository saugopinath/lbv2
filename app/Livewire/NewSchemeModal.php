<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Scheme;
use Livewire\Attributes\Validate;
use Livewire\Component;

class NewSchemeModal extends Component
{
    #[Validate('required|min:3|max:255|string|unique:schemes,name')]
    public string $schemeName = '';

    #[Validate('required|min:3|max:10|string|unique:schemes,short_name')]
    public string $schemeShortName = '';

    #[Validate('required')]
    public ?int $schemeDepartment = null;

    #[Validate('max:255|string')]
    public string $schemeDescription = '';

    public function render()
    {
        return view('livewire.new-scheme-modal', [
            'departments' => Department::select('id', 'name', 'short_name')->get() ?? [],
        ]);
    }

    public function updatedSchemeName(string $v)
    {
        $this->schemeName = strtoupper($v);
    }

    public function saveScheme()
    {
        $this->validate();

        Scheme::create([
            'name' => $this->schemeName,
            'short_name' => $this->schemeShortName,
            'description' => $this->schemeDescription ?? null,
            'department_id' => $this->schemeDepartment,
        ]);

        $this->reset();
        $this->dispatch('scheme-created');
        session()->flash('message', 'Scheme successfully created.');
    }
}
