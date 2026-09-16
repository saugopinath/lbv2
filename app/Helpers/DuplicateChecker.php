<?php

namespace App\Helpers;

use App\Models\BeneficiaryAadhaar;
use App\Models\BeneficiaryBankDetail;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\DupcheckschemeconfigSetting;
use App\Models\Scheme;

class DuplicateChecker
{
    public static function check($schemeId, $applicationId, array $formData, array $aadhaarPayload = [])
    {
        $configs = DupcheckschemeconfigSetting::where('scheme_id', $schemeId)->get();

        if ($configs->isEmpty()) {
            return true;
        }

        $labels = [
            'Aadhaar' => 'Aadhaar Number',
            'Bank'    => 'Account Number',
            'Mobile'  => 'Mobile Number',
            'CS'      => 'Caste Certificate Number',
        ];

        foreach ($configs as $config) {
            $type = $config->check_with; // 'Aadhaar', 'Bank', 'Mobile', 'CS'
            $inputValue = null;
            $formFieldName = '';
            $aadhaarHash = null;
            $encodedAadhaar = null;

            if ($type === 'Aadhaar') {
                $formFieldName = 'aadhaar_no';
                $aadhaarNo = trim($formData['aadhaar_no'] ?? $formData['aadhaar'] ?? '');
                $aadhaarHash = !empty($aadhaarNo) ? md5($aadhaarNo) : ($aadhaarPayload['hash'] ?? null);
                $encodedAadhaar = $aadhaarPayload['encoded'] ?? null;

                if (!$aadhaarHash && !$encodedAadhaar) {
                    continue;
                }
            } elseif ($type === 'Mobile') {
                $formFieldName = 'mobile_no';
                $inputValue = trim($formData['mobile_no'] ?? $formData['mobile'] ?? '');
                if (empty($inputValue)) continue;
            } elseif ($type === 'Bank') {
                $formFieldName = 'bankaccountnumber';
                $inputValue = trim($formData['bankaccountnumber'] ?? $formData['bank_account_no'] ?? '');
                if (empty($inputValue)) continue;
            } elseif ($type === 'CS') {
                $formFieldName = 'caste_cer_no';
                $inputValue = trim($formData['caste_cer_no'] ?? $formData['caste_certificate_no'] ?? '');
                if (empty($inputValue)) continue;
            } else {
                continue;
            }

            $label = $labels[$type] ?? $type;

            // 1. IS SAME CHECK (Within the same scheme, irrespective of module)
            if ($config->is_same) {
                $duplicateInSame = static::queryDuplicate($type, $schemeId, $inputValue, $aadhaarHash, $encodedAadhaar, $applicationId, true);

                if ($duplicateInSame) {
                    return [
                        'field' => "formData.{$formFieldName}",
                        'message' => "This {$label} is already registered in this scheme."
                    ];
                }
            }

            // 2. IS CROSS CHECK (Across other schemes, irrespective of module)
            if ($config->is_cross) {
                $targetSchemes = !empty($config->scheme_lists) && is_array($config->scheme_lists) ? $config->scheme_lists : null;
                $duplicateInCross = static::queryDuplicate($type, $schemeId, $inputValue, $aadhaarHash, $encodedAadhaar, $applicationId, false, $targetSchemes);

                if ($duplicateInCross) {
                    $foundSchemeName = Scheme::find($duplicateInCross->scheme_id)->name ?? 'another';
                    return [
                        'field' => "formData.{$formFieldName}",
                        'message' => "This {$label} is already registered in {$foundSchemeName} scheme."
                    ];
                }
            }
        }

        return true;
    }

    private static function queryDuplicate($type, $schemeId, $inputValue, $aadhaarHash, $encodedAadhaar, $applicationId, $isSameScheme, ?array $targetSchemes = null)
    {
        if ($type === 'Aadhaar') {
            $query = BeneficiaryAadhaar::query();
            if ($isSameScheme) {
                $query->where('scheme_id', $schemeId);
            } else {
                if (!empty($targetSchemes)) {
                    $query->whereIn('scheme_id', $targetSchemes);
                } else {
                    $query->where('scheme_id', '!=', $schemeId);
                }
            }

            if ($applicationId) {
                $query->where('application_id', '!=', (int)$applicationId);
            }

            $query->where(function ($q) use ($aadhaarHash, $encodedAadhaar) {
                if ($aadhaarHash) {
                    $q->orWhere('aadhaar_hash', $aadhaarHash);
                }
                if ($encodedAadhaar) {
                    $q->orWhere('encoded_aadhaar', $encodedAadhaar);
                }
            });

            return $query->first();
        }

        if ($type === 'Mobile') {
            $query = BeneficiaryPersonalDetail::query();
            if ($isSameScheme) {
                $query->where('scheme_id', $schemeId);
            } else {
                if (!empty($targetSchemes)) {
                    $query->whereIn('scheme_id', $targetSchemes);
                } else {
                    $query->where('scheme_id', '!=', $schemeId);
                }
            }

            if ($applicationId) {
                $query->where('application_id', '!=', (int)$applicationId);
            }

            $query->where(function ($q) use ($inputValue) {
                $q->whereRaw("other_details->>'mobile_no' = ?", [$inputValue])
                  ->orWhereRaw("TRIM(CAST(other_details->'mobile_no' AS TEXT)) = ?", ['"' . $inputValue . '"']);
            });

            return $query->first();
        }

        if ($type === 'Bank') {
            $query = BeneficiaryBankDetail::query();
            if ($isSameScheme) {
                $query->where('scheme_id', $schemeId);
            } else {
                if (!empty($targetSchemes)) {
                    $query->whereIn('scheme_id', $targetSchemes);
                } else {
                    $query->where('scheme_id', '!=', $schemeId);
                }
            }

            if ($applicationId) {
                $query->where('application_id', '!=', (int)$applicationId);
            }

            $query->whereRaw("TRIM(CAST(bankaccountnumber AS TEXT)) = ?", [$inputValue]);

            return $query->first();
        }

        if ($type === 'CS') {
            $query = BeneficiaryPersonalDetail::query();
            if ($isSameScheme) {
                $query->where('scheme_id', $schemeId);
            } else {
                if (!empty($targetSchemes)) {
                    $query->whereIn('scheme_id', $targetSchemes);
                } else {
                    $query->where('scheme_id', '!=', $schemeId);
                }
            }

            if ($applicationId) {
                $query->where('application_id', '!=', (int)$applicationId);
            }

            $query->whereRaw("TRIM(CAST(caste_cer_no AS TEXT)) = ?", [$inputValue]);

            return $query->first();
        }

        return null;
    }
}

