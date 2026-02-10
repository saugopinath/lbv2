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

    // মেইন কনফিগারেশন হোল্ডার
    public $config = [];

    public function mount($schemeId)
    {
        $this->schemeId = $schemeId;
        $this->dupcheckOptions = [
            'Aadhar' => 'Aadhar',
            'Bank'   => 'Bank',
            'Mobile' => 'Mobile',
        ];

        $this->schemeOptions = Scheme::where('id', '!=', $schemeId)
            ->pluck('name', 'id')
            ->toArray();

        // ডিফল্ট স্ট্রাকচার তৈরি করা
        foreach ($this->dupcheckOptions as $key => $label) {
            $this->config[$key] = [
                'selected' => false,
                'issame'  => 'no',
                'iscross'  => 'no',
                'schemes'  => []
            ];
        }

        // ডাটাবেস থেকে এক্সিস্টিং ডাটা লোড করা
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
        // ভ্যালিডেশন: কমপক্ষে একটি অপশন সিলেক্ট করতে হবে
        $selectedCount = collect($this->config)->where('selected', true)->count();
        if ($selectedCount === 0) {
            $this->addError('selected_error', 'Minimum one field must be selected.');
            return;
        }

        DB::beginTransaction();
        try {
            // পুরনো সেটিংস মুছে নতুনগুলো সেভ করা (Clean approach)
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
