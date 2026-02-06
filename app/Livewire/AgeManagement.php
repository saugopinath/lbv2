<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Exception;

class AgeManagement extends Component
{
    public $schemeId, $minage, $maxage, $isspecial = 'no', $specialcaseOptions = [], $specialcase;
    public function mount($schemeId)
    {
        $this->specialcaseOptions = collect([
            '1' => 'Farmer',
            '2'  => 'Widow',
        ])->map(function ($name, $id) {
            return (object) ['id' => $id, 'name' => $name];
        });
        $this->schemeId = $schemeId;
    }
    public function save()
    {
        $validated = $this->validate([
            'minage' => 'required|integer',
            'maxage' => 'required|integer',
        ]);
        DB::beginTransaction();
        try {
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ]);
        }
    }
    public function render()
    {
        return view('livewire.age-management');
    }
}
