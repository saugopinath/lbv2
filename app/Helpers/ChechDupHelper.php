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
            // dd($value);
            $existsInCommonList = BeneficiaryCommonList::where('encoded_aadhar', $value)
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
}
