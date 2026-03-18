<?php

namespace App\Helpers;

use App\Models\BeneficiaryAadhaar;
use App\Models\ApplicantIncompletDeatil;
use App\Models\BeneficiaryBankDetail;
use App\Models\BeneficiaryPersonalDetail;

class ChechDupHelper
{
    public static function checkDuplicate(string $type, string $value, string $incompleteType, string $schemeId)
    {
        if (!$value) {
            return true;
        }

        if ($type === 'aadhaar') {
            $aadhar = md5($value);

            $existsInCommonList = BeneficiaryAadhaar::where('encoded_aadhar', $aadhar)
                ->whereRelation('personal', 'scheme_id', $schemeId)
                ->whereRelation('personal', 'is_final', 1)
                ->whereRelation('personal', 'next_level_role_id', '!=', -100)
                ->exists();

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

            $existsInCommonList = BeneficiaryPersonalDetail::where('other_details->mobile_no', $value)
                ->where('scheme_id', $schemeId)
                ->where('is_final', 1)
                ->where('next_level_role_id', '!=', -100)
                ->exists();

            // dd($existsInCommonList);
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

            $existsInCommonList = BeneficiaryBankDetail::where('bankaccountnumber', $value)
                ->whereRelation('personal', 'scheme_id', $schemeId)
                ->whereRelation('personal', 'is_final', 1)
                ->whereRelation('personal', 'next_level_role_id', '!=', -100)
                ->exists();

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

    public static function checkBankMobileDuplicate(string $type, string $value, $schemeId)
    {
        $errors = [];

        if ($type === 'mobile') {

            $existsMobile = BeneficiaryPersonalDetail::where('other_details->mobile_no', $value)
                ->where('scheme_id', $schemeId)
                ->where('is_final', 1)
                ->where('next_level_role_id', '!=', -100)
                ->exists();

            if ($existsMobile) {
                $errors[] = "Duplicate found for Mobile: {$value}";
            }
        }

        if ($type === 'bank') {

            $existsBank = BeneficiaryBankDetail::where('bankaccountnumber', $value)
                ->whereHas('personal', function ($q) use ($schemeId) {
                    $q->where('scheme_id', $schemeId)
                        ->where('is_final', 1)
                        ->where('next_level_role_id', '!=', -100);
                })
                ->exists();

            if ($existsBank) {
                $errors[] = "Duplicate found for Bank Account: {$value}";
            }
        }

        return !empty($errors) ? implode(' | ', $errors) : true;
    }
}
