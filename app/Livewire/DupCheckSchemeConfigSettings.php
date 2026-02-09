<?php

namespace App\Livewire;

use App\Models\DupcheckschemeconfigSetting;
use App\Models\Scheme;
use App\Models\SchemeFinalSubmitCheck;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Exception;

class DupCheckSchemeConfigSettings extends Component
{
    public $dupcheckOptions = [], $selecteddupcheckOptions = [], $schemeOptions = [], $schemes = [], $iscross = 'no', $schemeId;
    public function mount($schemeId)
    {
        $this->schemeId = $schemeId;
        $this->dupcheckOptions = [
            'Aadhar' => 'Aadhar',
            'Bank' => 'Bank',
            'Mobile' => 'Mobile',
        ];
        $this->schemeOptions = Scheme::where('id', '!=', $schemeId)
            ->pluck('name', 'id')
            ->toArray();
        // SchemeFinalSubmitCheck::where('is_final_submitted', true)
        // ->where('scheme_id', '!=', $schemeId)
        // ->whereHas('scheme')
        // ->with('scheme')
        // ->get()
        // ->pluck('scheme.name', 'scheme.id')
        // ->toArray();

        $existingSettings = DupcheckschemeconfigSetting::where('scheme_id', $this->schemeId)->get();
        if ($existingSettings->isNotEmpty()) {
            $first = $existingSettings->first();
            $this->iscross = $first->is_cross ? 'yes' : 'no';
            $this->schemes = $first->scheme_lists ?? [];
            $this->selecteddupcheckOptions = $existingSettings->pluck('check_with')->toArray();
        }
    }

    // public function toggleAll()
    // {
    //     if (count($this->selecteddupcheckOptions) === count($this->dupcheckOptions)) {
    //         $this->selecteddupcheckOptions = [];
    //     } else {
    //         $this->selecteddupcheckOptions = array_keys($this->dupcheckOptions);
    //     }
    // }

    public function save()
    {
        $validated = $this->validate([
            'iscross' => 'required',
            'selecteddupcheckOptions' => 'required|array|min:1',
            'schemes' => 'required_if:iscross,yes',
        ], [
            'selecteddupcheckOptions.*' => 'Minimum one value should be checked',
            'schemes.*' => 'Minimum one value should be selected'
        ]);
        DB::beginTransaction();
        try {
            $exists = DupcheckschemeconfigSetting::where('scheme_id', $this->schemeId)->exists();
            $message = $exists
                ? 'Dup Check Scheme Config updated successfully!'
                : 'Dup Check Scheme Config saved successfully!';
            // DupcheckschemeconfigSetting::where('scheme_id', $this->schemeId)->delete();
            // foreach ($this->selecteddupcheckOptions as $option) {
            //     DupcheckschemeconfigSetting::create([
            //         'scheme_id'    => $this->schemeId,
            //         'is_cross'     => $this->iscross,
            //         'check_with'   => $option,
            //         'scheme_lists' => ($this->iscross === 'yes') ? $this->schemes : null,
            //     ]);
            // }
            $existingOptions = DupcheckschemeconfigSetting::where('scheme_id', $this->schemeId)
                ->pluck('check_with')
                ->toArray();
            DupcheckschemeconfigSetting::where('scheme_id', $this->schemeId)
                ->whereNotIn('check_with', $this->selecteddupcheckOptions)
                ->delete();
            foreach ($this->selecteddupcheckOptions as $option) {
                DupcheckschemeconfigSetting::updateOrCreate(
                    [
                        'scheme_id' => $this->schemeId,
                        'check_with' => $option,
                    ],
                    [
                        'is_cross'     => $this->iscross,
                        'scheme_lists' => ($this->iscross === 'yes') ? $this->schemes : null,
                    ]
                );
            }
            DB::commit();
            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => $message
            ]);
            return redirect()->route('duplicate-checks');
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
        return view('livewire.dup-check-scheme-config-settings');
    }
}
