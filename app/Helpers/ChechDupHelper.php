<?php

namespace App\Helpers;

use App\Models\BeneficiaryCommonList;
use App\Models\ApplicantIncompletDeatil;

class ChechDupHelper
{
    public static function checkDuplicate(string $type, string $value, string $incompleteType)
    {
        if (!$value) {
            return true;
        }

        if ($type === 'aadhaar') {
            $aadhar = md5($value);
            $existsInCommonList = BeneficiaryCommonList::where('encoded_aadhar', $aadhar)
                ->where('is_reject', false)->exists();

            $existsInIncomplete = ApplicantIncompletDeatil::whereJsonContains('new_value->aadhaar_no', $value)
                ->whereHas('incompleteType', function ($q) use ($incompleteType) {
                    $q->where('table_column', 'LIKE', "%{$incompleteType}%");
                })
                ->exists();

            if ($existsInCommonList || $existsInIncomplete) {
                return "Duplicate found for Aadhaar: {$value}";
            }

            return true;
        }

        if ($type === 'mobile') {
            $existsInCommonList = BeneficiaryCommonList::where('mobile_no', $value)
                ->where('is_reject', false)->exists();

            $existsInIncomplete = ApplicantIncompletDeatil::whereJsonContains('new_value->mobile_no', $value)
                ->whereHas('incompleteType', function ($q) use ($incompleteType) {
                    $q->where('table_column', 'LIKE', "%{$incompleteType}%");
                })
                ->exists();

            if ($existsInCommonList || $existsInIncomplete) {
                return "Duplicate found for Mobile: {$value}";
            }

            return true;
        }

        if ($type === 'bank') {
            $existsInCommonList = BeneficiaryCommonList::where('bank_account_number', $value)
                ->where('is_reject', false)->exists();

            $existsInIncomplete = ApplicantIncompletDeatil::whereJsonContains('new_value->account_number', $value)
                ->whereHas('incompleteType', function ($q) use ($incompleteType) {
                    $q->where('table_column', 'LIKE', "%{$incompleteType}%");
                })
                ->exists();

            if ($existsInCommonList || $existsInIncomplete) {
                return "Duplicate found for Bank Account: {$value}";
            }

            return true;
        }

        return "Invalid check type!";
    }

    public static function checkBankMobileDuplicate(string $type, string $value)
    {
        $errors = [];

        if ($type === 'mobile') {
            // dd($value);
            $existsMobile = BeneficiaryCommonList::where('mobile_no', $value)
                ->where('is_reject', false)
                ->exists();

            if ($existsMobile) {
                $errors[] = "Duplicate found for Mobile: {$value}";
            }
        }

        if ($type === 'bank') {
            // dd($value);
            $existsBank = BeneficiaryCommonList::where('bank_account_number', $value)
                ->where('is_reject', false)
                ->exists();

            if ($existsBank) {
                $errors[] = "Duplicate found for Bank Account: {$value}";
            }
        }

        if (!empty($errors)) {
            return implode(' | ', $errors); 
        }

        return true; 
    }
}
