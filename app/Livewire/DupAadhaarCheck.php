<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BeneficiaryAadhaar;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AadhaarHelper;
use App\Helpers\WorkFlowPermissionHelper;
use Illuminate\Support\Facades\Session;
use App\Models\Scheme;
use App\Interfaces\DuplicatecheckInterface;

class DupAadhaarCheck extends Component
{
    public $aadhaar;
    public $error = null;
    public $schemeId;
    public $showDsTable = false;
    // public function checkDuplicate()
    // {
    //     $this->error = null;
    //     $this->aadhaar = trim($this->aadhaar);
    //     if (!AadhaarHelper::validate($this->aadhaar)) {
    //         $this->error = "Invalid Aadhaar number sc";
    //         $this->dispatch('hideLoader');
    //         return ['status' => 'error', 'message' => $this->error];
    //     }
    //     $encoded_aadhar = Crypt::encryptString($this->aadhaar);
    //     $aadhaar_hash = md5($this->aadhaar);
    //     if (BeneficiaryAadhaar::where('aadhar_hash', $aadhaar_hash)->exists()) {
    //         $this->error = "Duplicate Aadhaar found!";
    //         $this->dispatch('hideLoader');
    //         return ['status' => 'duplicate', 'message' => $this->error];
    //     }       

    //     $this->dispatch('aadhaarChecked', [
    //         'encoded' => $encoded_aadhar,
    //         'hash' => $aadhaar_hash,           
    //     ]);
    //     $this->dispatch('hideLoader');
    //     return ['status' => 'success', 'message' => '✅ Aadhaar is valid and not duplicate.'];
    // }
    public function mount($schemeId = null)
    {
        $this->schemeId = $schemeId;
    }
    public function checkDuplicate()
    {
        $this->error = null;
        $this->aadhaar = trim($this->aadhaar);
        if (!AadhaarHelper::validate($this->aadhaar)) {
            $this->error = "Invalid Aadhaar number";
            $this->dispatch('hideLoader');
            return ['status' => 'error', 'message' => $this->error];
        }
        $encoded_aadhaar = Crypt::encryptString($this->aadhaar);
        $aadhaar_hash = md5($this->aadhaar);

        /*$exists = BeneficiaryAadhaar::where('aadhar_hash', $aadhaar_hash)
            ->where('scheme_id', $this->schemeId)
            ->exists();
        if ($exists) {
            $this->error = "Duplicate Aadhaar found for this scheme!";
            $this->dispatch('hideLoader');
            return [
                'status' => 'duplicate',
                'message' => $this->error,
                'ds_entry' => WorkFlowPermissionHelper::canDuareSarkarEntryAllow()
            ];
        }
        $this->dispatch('aadhaarChecked', [
            'encoded' => $encoded_aadhar,
            'hash' => $aadhaar_hash
        ]);
        $this->dispatch('hideLoader');*/

        // TODO: make it generic for all schemes 
        $configs = Scheme::with('duplicateCheckSettings')->findOrFail($this->schemeId);
        if ($configs) {
            foreach ($configs->duplicateCheckSettings as $config) {
                $type = $config->check_with;
                if ($type === 'Aadhaar') {
                    if ($config->is_same) {
                        $existsSame = BeneficiaryAadhaar::where('aadhaar_hash', $aadhaar_hash)
                            ->where('scheme_id', $this->schemeId)
                            ->exists();
                        if ($existsSame) {
                            $this->error = "Duplicate Aadhaar found for this scheme!";
                            $this->dispatch('hideLoader');
                            return [
                                'status' => 'duplicate',
                                'message' => $this->error,
                                'ds_entry' => WorkFlowPermissionHelper::canDuareSarkarEntryAllow()
                            ];
                        }
                    } if ($config->is_cross && !empty($config->scheme_lists)) {
                        $schemeLists = is_array($config->scheme_lists) ? $config->scheme_lists : json_decode($config->scheme_lists, true);
                        $otherSchemes = implode(',', $schemeLists);
                        $checkWith = $config->check_with;
                        $data = app(DuplicatecheckInterface::class)->duplicatecheck($checkWith, $this->schemeId, $this->aadhaar, $otherSchemes);
                        if ($data && isset($data->isdup) && $data->isdup) {
                            $type = $data->checkWith;
                            $scheme_name = Scheme::find($data->scheme)->name ?? 'another';
                            $this->error = "This $type is already registered in $scheme_name scheme.";
                            $this->dispatch('hideLoader');
                            return [
                                'status' => 'duplicate',
                                'message' => $this->error
                            ];
                        }
                    }
                }
            }
            $this->dispatch('aadhaarChecked', [
                    'encoded' => $encoded_aadhaar,
                    'hash' => $aadhaar_hash
                ]);
            $this->dispatch('hideLoader');
        } else {
            $this->dispatch('aadhaarChecked', [
                'encoded' => $encoded_aadhaar,
                'hash' => $aadhaar_hash
            ]);
            $this->dispatch('hideLoader');
        }
        // TODO: make it generic for all schemes
        return ['status' => 'success', 'message' => '✅ Aadhaar is valid and not duplicate.'];
    }
    public function DsMark()
    {
        // Session::put('dup_aadhaar', md5(trim($this->aadhaar)));
        $this->showDsTable = true;
        $this->dispatch('aadhaarCheckedds', [
            'aadhaar_hash' => md5(trim($this->aadhaar))
        ]);
        return [
            'status' => true
        ];
    }
    public function render()
    {
        return view('livewire.dup-aadhaar-check');
    }
}
