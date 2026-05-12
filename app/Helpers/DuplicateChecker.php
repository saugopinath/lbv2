<?php
namespace App\Helpers;
use App\Interfaces\DuplicatecheckInterface;
use App\Models\BeneficiaryAadhaar;
use App\Models\BeneficiaryBankDetail;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Scheme;
class DuplicateChecker
{
    public static function check($schemeId, $applicationId, array $formData, array $aadhaarPayload = [])
    {
        $configs = Scheme::with('duplicateCheckSettings')->findOrFail($schemeId);
        foreach ($configs->duplicateCheckSettings as $config) {
            $type = $config->check_with;
            $inputValue = null;
            $column = '';
            $formFieldName = '';
            $modelClass = null;
            if ($type === 'Aadhaar') {
                $modelClass = BeneficiaryAadhaar::class;
                $column = 'encoded_aadhaar';
                $inputValue = $aadhaarPayload['encoded'] ?? null;
                $formFieldName = 'aadhaar_no';
            } elseif ($type === 'Mobile') {
                $modelClass = BeneficiaryPersonalDetail::class;
                $inputValue = trim($formData['mobile_no'] ?? '');
                $formFieldName = 'mobile_no';
            } elseif ($type === 'Bank') {
                $modelClass = BeneficiaryBankDetail::class;
                $column = 'bankaccountnumber';
                $inputValue = trim($formData['bankaccountnumber'] ?? '');
                $formFieldName = 'bankaccountnumber';
            }
            if (empty($inputValue) || !$modelClass) continue;
            if ($config->is_same) {
                $existsSame = $modelClass::where('scheme_id', $schemeId)
                    ->where(function ($query) use ($type, $column, $inputValue) {
                        if ($type === 'Mobile') {
                            $query->where('other_details->mobile_no', $inputValue);
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
                $schemeLists = is_array($config->scheme_lists) ? $config->scheme_lists : json_decode($config->scheme_lists, true);
                $otherSchemes = implode(',', $schemeLists);
                $checkWith = $config->check_with;
                $data = app(DuplicatecheckInterface::class)->duplicatecheck($checkWith, $schemeId, $inputValue, $otherSchemes);
                if ($data && isset($data->isdup) && $data->isdup) {
                    $type = $data->checkWith;
                    $scheme_name = Scheme::find($data->scheme)->name ?? 'another';
                    if ($data->checkWith == 'Aadhaar') {
                        $formFieldName = 'aadhaar_no';
                    } elseif ($data->checkWith == 'Mobile') {
                        $formFieldName = 'mobile_no';
                    } elseif ($data->checkWith == 'Bank') {
                        $formFieldName = 'bankaccountnumber';
                    }
                    return [
                        'field' => "formData.{$formFieldName}",
                        'message' => "This $type is already registered in $scheme_name scheme."
                    ];
                }
            }
        }
        return true;
    }
}
