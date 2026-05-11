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
        $rules = [];
        $messages = [];
        foreach ($this->config as $key => $data) {
            if ($data['selected'] && $data['iscross'] === 'yes') {
                $rules["config.$key.schemes"] = 'required|array|min:1';
                $messages["config.$key.schemes.required"] = "At least one scheme must be selected for {$this->dupcheckOptions[$key]}.";
                $messages["config.$key.schemes.min"] = "At least one scheme must be selected for {$this->dupcheckOptions[$key]}.";
            }
        }
        if (!empty($rules)) {
            $this->validate($rules, $messages);
        }

        DB::beginTransaction();
        try {
            DupcheckschemeconfigSetting::where('scheme_id', $this->schemeId)->delete();

            foreach ($this->config as $optionName => $data) {
                if ($data['selected']) {
                    $setting = new DupcheckschemeconfigSetting();
                    $setting->scheme_id = $this->schemeId;
                    $setting->check_with = $optionName;
                    $setting->is_same = $data['issame'] === 'yes' ? true : false;
                    $setting->is_cross = $data['iscross'] === 'yes' ? true : false;
                    $setting->scheme_lists = ($data['iscross'] === 'yes') ? $data['schemes'] : null;
                    $setting->save();
                }
            }

            DB::commit();
            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'Config saved successfully!'
            ]);
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