<?php

namespace App\Helpers;

use App\Models\BeneficiaryAadhaar;
use App\Models\BeneficiaryBankDetail;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\DupcheckschemeconfigSetting;

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
        $configs = DupCheckSchemeConfigSetting::where('scheme_id', $schemeId)
            ->orderByRaw("CASE 
            WHEN check_with = 'Aadhar' THEN 1 
            WHEN check_with = 'Mobile' THEN 2 
            WHEN check_with = 'Bank' THEN 3 
            ELSE 4 END ASC")
            ->get();
        foreach ($configs as $config) {
            $type = $config->check_with;
            $model = null;
            $column = '';
            $inputValue = null;
            $formFieldName = '';
            if ($type === 'Aadhar') {
                $model = new BeneficiaryAadhaar();
                $column = 'encoded_aadhar';
                $inputValue = $aadhaarPayload['encoded'] ?? null;
                $formFieldName = 'aadhar_no';
            } elseif ($type === 'Mobile') {
                $model = new BeneficiaryPersonalDetail();
                $formFieldName = 'mobile_no';
                $inputValue = trim($formData['mobile_no'] ?? '');
            } elseif ($type === 'Bank') {
                $model = new BeneficiaryBankDetail();
                $column = 'bankaccountnumber';
                $formFieldName = 'bankaccountnumber';
                $inputValue = trim($formData['bankaccountnumber'] ?? '');
            }
            if (empty($inputValue) || !$model) continue;
            $queryBuilder = function ($query) use ($type, $column, $inputValue) {
                if ($type === 'Mobile') {
                    $query->where('other_details->mobile_no', $inputValue);
                } else {
                    $query->where($column, $inputValue);
                }
            };
            if ($config->is_same) {
                $existsSame = $model::where('scheme_id', $schemeId)
                    ->where($queryBuilder)
                    ->where('application_id', '!=', $applicationId)
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
                
            }
        }
        return true;
    }
}
