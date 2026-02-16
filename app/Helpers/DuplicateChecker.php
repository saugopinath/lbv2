<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DuplicateChecker
{
    /**
     * @param int $schemeId
     * @param int|null $applicationId
     * @param array $formData
     * @param array $aadhaarPayload
     * @return array|bool
     */
    public static function check($schemeId, $applicationId, array $formData, array $aadhaarPayload = [])
    {
        $configs = DB::table('public.dupcheckschemeconfig_settings')
            ->where('scheme_id', $schemeId)
            ->orderByRaw("CASE 
                WHEN check_with = 'Aadhar' THEN 1 
                WHEN check_with = 'Mobile' THEN 2 
                WHEN check_with = 'Bank' THEN 3 
                ELSE 4 END ASC")
            ->get();
        foreach ($configs as $config) {
            $type = $config->check_with;
            $inputValue = null;
            $table = '';
            $column = '';
            $formFieldName = '';
            if ($type === 'Aadhar') {
                $table = 'pension.beneficiary_aadhars';
                $column = 'encoded_aadhar';
                $inputValue = $aadhaarPayload['encoded'] ?? null;
                $formFieldName = 'aadhar_no';
            } elseif ($type === 'Mobile') {
                $table = 'pension.beneficiary_personals';
                $inputValue = trim($formData['mobile_no'] ?? '');
                $formFieldName = 'mobile_no';
            } elseif ($type === 'Bank') {
                $table = 'pension.beneficiary_banks';
                $column = 'bankaccountnumber';
                $inputValue = trim($formData['bankaccountnumber'] ?? '');
                $formFieldName = 'bankaccountnumber';
            }
            if (empty($inputValue)) continue;
            if ($config->is_same) {
                $existsSame = DB::table($table)
                    ->where('scheme_id', $schemeId)
                    ->where(function ($query) use ($type, $column, $inputValue) {
                        if ($type === 'Mobile') {
                            $query->whereRaw("other_details->>'mobile_no' = ?", [$inputValue]);
                        } else {
                            $query->whereRaw("TRIM(CAST($column AS TEXT)) = ?", [$inputValue]);
                        }
                    })
                    ->where('application_id', '!=', (int)$applicationId)
                    ->exists();
                if ($existsSame) {
                    return [
                        'field' => "formData.{$formFieldName}",
                        'message' => "This $type is already registered in this scheme."
                    ];
                }
            }
            if ($config->is_cross && !empty($config->scheme_lists)) {
                $otherSchemes = json_decode($config->scheme_lists, true);
                $existsCross = DB::table($table)
                    ->whereIn('scheme_id', $otherSchemes)
                    ->where(function ($query) use ($type, $column, $inputValue) {
                        if ($type === 'Mobile') {
                            $query->whereRaw("other_details->>'mobile_no' = ?", [$inputValue]);
                        } else {
                            $query->whereRaw("TRIM(CAST($column AS TEXT)) = ?", [$inputValue]);
                        }
                    })
                    ->exists();
                if ($existsCross) {
                    return [
                        'field' => "formData.{$formFieldName}",
                        'message' => "This $type is already registered in another scheme."
                    ];
                }
            }
        }
        return true;
    }
}
