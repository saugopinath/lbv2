<?php

namespace App\Livewire;

use App\Models\DupcheckschemeconfigSetting;
use App\Models\Scheme;
use App\Models\DynamicWorkflowSchemeModule;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Exception;

class DupCheckSchemeConfigSettings extends Component
{
    public $schemeId;
    public $moduleId;
    public $schemeModuleId;
    public bool $isEdit = false;
    public bool $already = false;
    public $dupcheckOptions = [];
    public $schemeOptions = [];
    public $config = [];

    public function mount($schemeId, $moduleId, $isEdit = false)
    {
        $this->isEdit = $isEdit;
        $this->schemeId = $schemeId;
        $this->moduleId = $moduleId;
        $this->dupcheckOptions = [
            'Aadhaar' => 'Aadhaar',
            'Bank'   => 'Bank',
            'Mobile' => 'Mobile',
            'CS' => 'Caste Certificate Number',
        ];

        $this->schemeOptions = Scheme::select('id', 'name')
            ->where('id', '!=', $schemeId)
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
        $this->schemeModuleId = DynamicWorkflowSchemeModule::where('scheme_id', $this->schemeId)
            ->where('module_id', $this->moduleId)
            ->value('id');

        $existingSettings = collect([]);
        if ($this->schemeModuleId) {
            $existingSettings = DupcheckschemeconfigSetting::select('check_with', 'is_same', 'is_cross', 'scheme_lists')
                ->where('scheme_id', $this->schemeId)
                ->where('module_id', $this->schemeModuleId)
                ->get();
        }
        if ($existingSettings->isEmpty()) {
            $existingSettings = DupcheckschemeconfigSetting::select('check_with', 'is_same', 'is_cross', 'scheme_lists')
                ->where('scheme_id', $this->schemeId)
                ->where('module_id', $this->moduleId)
                ->get();
        }
        if ($existingSettings->isEmpty()) {
            $existingSettings = DupcheckschemeconfigSetting::select('check_with', 'is_same', 'is_cross', 'scheme_lists')
                ->where('scheme_id', $this->schemeId)
                ->whereNull('module_id')
                ->get();
        }

        if ($existingSettings->isNotEmpty()) {
            $this->already = true;
            foreach ($existingSettings as $setting) {
                if (isset($this->config[$setting->check_with])) {
                    $schemes = $setting->scheme_lists;
                    if (is_string($schemes)) {
                        $schemes = json_decode($schemes, true) ?: [];
                    } elseif (!is_array($schemes)) {
                        $schemes = [];
                    }

                    $this->config[$setting->check_with] = [
                        'selected' => true,
                        'issame'  => ($setting->is_same && $setting->is_same !== 'no' && $setting->is_same !== '0') ? 'yes' : 'no',
                        'iscross'  => ($setting->is_cross && $setting->is_cross !== 'no' && $setting->is_cross !== '0') ? 'yes' : 'no',
                        'schemes'  => $schemes
                    ];
                }
            }
        }
    }

    public function enableEditing()
    {
        $this->isEdit = true;
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
            /* OLD CODE PRESERVED FOR BACKWARD COMPATIBILITY:
            if (!$this->schemeModuleId) {
                $this->schemeModuleId = DynamicWorkflowSchemeModule::where('scheme_id', $this->schemeId)
                    ->where('module_id', $this->moduleId)
                    ->value('id');
            }
            $targetModuleId = $this->schemeModuleId ?: $this->moduleId;
            */

            // REQUIREMENT: Enforce SchemeModuleID is resolved
            if (!$this->schemeModuleId) {
                $schemeModule = DynamicWorkflowSchemeModule::firstOrCreate(
                    [
                        'scheme_id' => $this->schemeId,
                        'module_id' => $this->moduleId,
                    ]
                );
                $this->schemeModuleId = $schemeModule->id;
            }

            $targetModuleId = $this->schemeModuleId;

            if ($targetModuleId) {
                DupcheckschemeconfigSetting::where('scheme_id', $this->schemeId)
                    ->where(function ($q) use ($targetModuleId) {
                        $q->where('module_id', $targetModuleId)
                          ->orWhere('module_id', $this->moduleId);
                    })
                    ->delete();

                $insertData = [];
                foreach ($this->config as $optionName => $item) {
                    if ($item['selected']) {
                        $insertData[] = [
                            'scheme_id'    => $this->schemeId,
                            'module_id'    => $targetModuleId,
                            'check_with'   => $optionName,
                            'is_same'      => $item['issame'] === 'yes',
                            'is_cross'     => $item['iscross'] === 'yes',
                            'scheme_lists' => ($item['iscross'] === 'yes') ? json_encode($item['schemes']) : null,
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ];
                    }
                }

                if (!empty($insertData)) {
                    DupcheckschemeconfigSetting::insert($insertData);
                }
            }

            DB::commit();
            $this->already = true;
            $this->isEdit = false;
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
