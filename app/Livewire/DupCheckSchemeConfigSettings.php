<?php

namespace App\Livewire;

use App\Models\DupcheckschemeconfigSetting;
use App\Models\Scheme;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Exception;

class DupCheckSchemeConfigSettings extends Component
{
    public $schemeId;
    public $dupcheckOptions = [];
    public $schemeOptions = [];
    public $config = [];

    public function mount($schemeId)
    {
        $this->schemeId = $schemeId;
        $this->dupcheckOptions = [
            'Aadhar' => 'Aadhar',
            'Bank'   => 'Bank',
            'Mobile' => 'Mobile',
            'CS' => 'Caste Certificate Number',
        ];

        $this->schemeOptions = Scheme::where('id', '!=', $schemeId)
            ->pluck('name', 'id')
            ->toArray();
        foreach ($this->dupcheckOptions as $key => $label) {
            $this->config[$key] = [
                'selected' => false,
                'issame'  => 'no',
                'iscross'  => 'no',
                'schemes'  => []
            ];
        }
        $existingSettings = DupcheckschemeconfigSetting::where('scheme_id', $this->schemeId)->get();
        foreach ($existingSettings as $setting) {
            if (isset($this->config[$setting->check_with])) {
                $this->config[$setting->check_with] = [
                    'selected' => true,
                    'issame'  => $setting->is_same ? 'yes' : 'no',
                    'iscross'  => $setting->is_cross ? 'yes' : 'no',
                    'schemes'  => $setting->scheme_lists ?? []
                ];
            }
        }
    }

    public function save()
    {
        

        DB::beginTransaction();
        try {
            DupcheckschemeconfigSetting::where('scheme_id', $this->schemeId)->delete();

            foreach ($this->config as $optionName => $data) {
                if ($data['selected']) {
                    DupcheckschemeconfigSetting::create([
                        'scheme_id'    => $this->schemeId,
                        'check_with'   => $optionName,
                        'is_same'     => $data['issame'] === 'yes' ? true : false,
                        'is_cross'     => $data['iscross'] === 'yes' ? true : false,
                        'scheme_lists' => ($data['iscross'] === 'yes') ? $data['schemes'] : null,
                    ]);
                }
            }

            DB::commit();
            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'Config saved successfully!'
            ]);

            return redirect()->route('duplicate-checks');
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.dup-check-scheme-config-settings');
    }
}