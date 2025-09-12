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

        // --------------------
        // Aadhaar Check
        // --------------------
        if ($type === 'aadhaar') {
            $existsInCommonList = BeneficiaryCommonList::where('aadhaar_no', $value)->exists();

            $existsInIncomplete = ApplicantIncompletDeatil::whereJsonContains('new_value->aadhaar_no', $value)
                ->whereHas('incompleteType', function ($q) use ($incompleteType) {
                    $q->where('name', 'LIKE', "%{$incompleteType}%");
                })
                ->exists();

            if ($existsInCommonList || $existsInIncomplete) {
                return "Duplicate found for {$incompleteType} (Aadhaar: {$value})";
            }

            return true;
        }

        // --------------------
        // Mobile Check
        // --------------------
        if ($type === 'mobile') {
            $existsInCommonList = BeneficiaryCommonList::where('mobile_no', $value)->exists();

            $existsInIncomplete = ApplicantIncompletDeatil::whereJsonContains('new_value->mobile_no', $value)
                ->whereHas('incompleteType', function ($q) use ($incompleteType) {
                    $q->where('name', 'LIKE', "%{$incompleteType}%");
                })
                ->exists();

            if ($existsInCommonList || $existsInIncomplete) {
                return "Duplicate found for {$incompleteType} (Mobile: {$value})";
            }

            return true;
        }

        // --------------------
        // Bank Account Check
        // --------------------
        if ($type === 'bank') {
            $existsInCommonList = BeneficiaryCommonList::whereHas('bank', function ($q) use ($value) {
                $q->where('account_number', $value);
            })->exists();

            $existsInIncomplete = ApplicantIncompletDeatil::whereJsonContains('new_value->account_number', $value)
                ->whereHas('incompleteType', function ($q) use ($incompleteType) {
                    $q->where('name', 'LIKE', "%{$incompleteType}%");
                })
                ->exists();

            if ($existsInCommonList || $existsInIncomplete) {
                return "Duplicate found for {$incompleteType} (Account: {$value})";
            }

            return true;
        }

        // --------------------
        // Invalid type
        // --------------------
        return "Invalid check type!";
    }
}
