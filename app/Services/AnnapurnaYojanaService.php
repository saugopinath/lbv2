<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnnapurnaYojanaService
{
    /**
     * Compile and save the application data to the database (draft or final submission).
     *
     * @param array $formData
     * @param array $members
     * @param int|null $familyId
     * @param string|null $appId
     * @param string $status
     * @return array Contains success status, familyId, and appId
     * @throws \Exception
     */
    public function saveApplication(
        array $formData,
        array $members,
        ?int $familyId,
        ?string $appId,
        string $status
    ): array {
        DB::connection('pgsql_annapurna')->beginTransaction();

        try {
            $selectLgd = session('lgd_session');
            $createdByDistCode = null;
            $createdByLocalBodyCode = null;

            if (! empty($selectLgd['district_id'])) {
                $createdByDistCode = (int) Crypt::decryptString($selectLgd['district_id']);
            }
            if (! empty($selectLgd['block_id'])) {
                $createdByLocalBodyCode = (int) Crypt::decryptString($selectLgd['block_id']);
            }
            if (! empty($selectLgd['subdivision_id'])) {
                $createdByLocalBodyCode = (int) Crypt::decryptString($selectLgd['subdivision_id']);
            }

            // 1. Resolve LGD location codes directly (inputs are verified during validation)
            $lgdDistrictCode = !empty($formData['district_id']) ? (int) $formData['district_id'] : 0;
            $lgdBlockMcCode = !empty($formData['blockurban']) ? (int) $formData['blockurban'] : 0;
            $lgdGpWardCode = !empty($formData['gpward']) ? (int) $formData['gpward'] : 0;

            // 2. Construct address string
            $addressParts = [];
            if (!empty($formData['house_no'])) {
                $addressParts[] = trim($formData['house_no']);
            }
            if (!empty($formData['village_town'])) {
                $addressParts[] = trim($formData['village_town']);
            }
            if (!empty($formData['post_office'])) {
                $addressParts[] = 'P.O. ' . trim($formData['post_office']);
            }
            if (!empty($formData['police_station'])) {
                $addressParts[] = 'P.S. ' . trim($formData['police_station']);
            }
            if (!empty($formData['pincode'])) {
                $addressParts[] = 'PIN ' . trim($formData['pincode']);
            }
            $address = implode(', ', $addressParts);

            // 3. Generate UUID for application_id if not present
            if (empty($appId)) {
                $appId = (string) Str::uuid();
            }

            // 4. Clean conditional fields and prepare variables
            $hasDigitalRationCard = (($formData['has_digital_ration_card'] ?? '') === 'Yes');
            $rationCardHouseholdId = $hasDigitalRationCard ? ($formData['hof_ration_card_id'] ?? null) : null;
            $rationCardType = $hasDigitalRationCard ? ($formData['ration_card_type'] ?? null) : null;
            $liftingMonthlyRation = $hasDigitalRationCard && (($formData['is_lifting_ration'] ?? '') === 'Yes');

            $hasConstitutionalPost = (($formData['has_constitutional_post'] ?? '') === 'Yes');
            $constitutionalPostDetails = $hasConstitutionalPost ? ($formData['constitutional_post_details'] ?? null) : null;

            $hasGstReg = (($formData['has_gst_reg'] ?? '') === 'Yes');
            $gstin = $hasGstReg ? ($formData['gstin'] ?? null) : null;

            $hasPensioner = (($formData['has_pensioner'] ?? '') === 'Yes');
            $pensionerDetails = $hasPensioner ? ($formData['pensioner_details'] ?? null) : null;

            $caaStatus = $formData['hof_caa_status'] ?? 'Not Applicable';
            $caaAppNo = $caaStatus === 'Applied' ? ($formData['hof_caa_app_no'] ?? null) : null;
            $caaCertNo = $caaStatus === 'Issued' ? ($formData['hof_caa_cert_no'] ?? null) : null;

            $sirStatus = $formData['hof_sir_status'] ?? 'Not Applicable';
            $sirCaseDetails = $sirStatus === 'Yes' ? ($formData['hof_sir_case_details'] ?? null) : null;

            $hasHealthInsurance = (($formData['has_health_insurance'] ?? '') === 'Yes');
            $healthInsuranceType = $hasHealthInsurance ? ($formData['health_insurance_type'] ?? null) : null;
            $healthInsurancePremium = ($hasHealthInsurance && !empty($formData['health_insurance_premium'])) ? (float) $formData['health_insurance_premium'] : null;
            $healthInsuranceSumAssured = ($hasHealthInsurance && !empty($formData['health_insurance_sum_assured'])) ? (float) $formData['health_insurance_sum_assured'] : null;

            $ownsLand = (($formData['owns_land'] ?? '') === 'Yes');
            $landSizeDecimals = ($ownsLand && !empty($formData['land_size_decimals'])) ? (float) $formData['land_size_decimals'] : null;

            $hasFourWheeler = (($formData['owns_4_wheeler'] ?? '') === 'Yes');
            $vehicleCount = $hasFourWheeler && !empty($formData['num_vehicles']) ? (int) $formData['num_vehicles'] : null;
            $vehicleReg = $hasFourWheeler && !empty($formData['vehicles']) ? json_encode(array_column($formData['vehicles'], 'reg_no')) : null;
            $vehicleModel = $hasFourWheeler && !empty($formData['vehicles']) ? json_encode(array_column($formData['vehicles'], 'model')) : null;

            // 5. Update or Insert family details
            $familyData = [
                'application_id' => $appId,
                'total_family_members' => (int) ($formData['num_family_members'] ?? 1),
                'lifting_monthly_ration' => $liftingMonthlyRation,
                'has_electricity_connection' => false,
                'is_agreed' => (bool) ($formData['agree_consent'] ?? false),
                'application_status' => $status,
                'lgd_district_code' => $lgdDistrictCode,
                'lgd_block_mc_code' => $lgdBlockMcCode,
                'lgd_gp_ward_code' => $lgdGpWardCode,
                'address' => $address,
                'has_digital_ration_card' => $hasDigitalRationCard,
                'ration_card_household_id' => $rationCardHouseholdId,
                'no_of_illiterate_adults' => !empty($formData['num_illiterate_adults']) ? (int) $formData['num_illiterate_adults'] : null,
                'no_of_literate_adults' => !empty($formData['num_literate_adults']) ? (int) $formData['num_literate_adults'] : null,
                'total_annual_family_income' => !empty($formData['total_annual_income']) ? (int) $formData['total_annual_income'] : null,
                'area_type' => ($formData['rural_urban'] ?? '') == 2 ? 'RURAL' : (($formData['rural_urban'] ?? '') == 1 ? 'URBAN' : null),
                'ulb' => ($formData['rural_urban'] ?? '') == 1 ? (int) ($formData['blockurban'] ?? null) : null,
                'updated_at' => now(),
                'created_by_dist_code' => $createdByDistCode,
                'created_by_local_body_code' => $createdByLocalBodyCode,
            ];

            if ($familyId) {
                DB::connection('pgsql_annapurna')->table('dbt_apy.families')->where('id', $familyId)->update($familyData);
            } else {
                $familyData['created_at'] = now();
                $familyId = DB::connection('pgsql_annapurna')->table('dbt_apy.families')->insertGetId($familyData, 'id');
            }

            // 6. Clear existing related records to prevent duplication
            $existingMemberIds = DB::connection('pgsql_annapurna')
                ->table('dbt_apy.family_members')
                ->where('family_id', $familyId)
                ->pluck('id')
                ->toArray();

            if (!empty($existingMemberIds)) {
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->whereIn('family_member_id', $existingMemberIds)->delete();
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->whereIn('family_member_id', $existingMemberIds)->delete();
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->whereIn('family_member_id', $existingMemberIds)->delete();
                DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->where('family_id', $familyId)->delete();
            }

            // 7. Save Head of Family (HOF)
            $isHofEligible = $this->isFemale25to60($formData['hof_gender'] ?? '', $formData['hof_dob'] ?? '');
            $hofMemberId = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->insertGetId([
                'family_id' => $familyId,
                'is_hof' => true,
                'member_name' => $formData['hof_name'] ?? '',
                'aadhaar_no' => $formData['hof_aadhaar'] ?? '',
                'mobile_no' => $formData['contact_no'] ?? null,
                'date_of_birth' => !empty($formData['hof_dob']) ? $formData['hof_dob'] : null,
                'gender' => $formData['hof_gender'] ?? null,
                'digital_ration_card_no' => $rationCardHouseholdId,
                'digital_ration_card_type' => $rationCardType,
                'social_category' => $formData['category'] ?? null,
                'bank_name' => $formData['hof_bank_name'] ?? null,
                'bank_account_no' => $formData['hof_acc_no'] ?? null,
                'ifsc_code' => $formData['hof_ifsc'] ?? null,
                'epic_no' => $formData['hof_epic_no'] ?? null,
                'assembly_constituency_no' => $formData['hof_assembly_constituency'] ?? null,
                'part_no' => $formData['hof_part_no'] ?? null,
                'caa_application_status' => $caaStatus,
                'caa_application_no' => $caaAppNo,
                'caa_certificate_no' => $caaCertNo,
                'sir2026tribunal_status' => $sirStatus,
                'sir2026case_details' => $sirCaseDetails,
                'has_four_wheeler' => $hasFourWheeler,
                'vehicle_count' => $vehicleCount,
                'vehicle_registration_no' => $vehicleReg,
                'vehicle_model' => $vehicleModel,
                'has_health_insurance' => $hasHealthInsurance,
                'health_insurance_type' => $healthInsuranceType === 'None' ? null : $healthInsuranceType,
                'health_insurance_sum_assured' => $healthInsuranceSumAssured,
                'health_insurance_annual_premium' => $healthInsurancePremium,
                'literacy_status' => $formData['hof_literate_status'] ?? null,
                'highest_educational_qualifications' => $formData['hof_highest_qualification'] ?? null,
                'gross_annual_income' => !empty($formData['total_annual_income']) ? (float) $formData['total_annual_income'] : null,
                'pays_income_or_professional_tax' => (($formData['pays_tax'] ?? '') === 'Yes'),
                'pan_no' => (($formData['has_pan_card'] ?? '') === 'Yes') ? ($formData['hof_pan_no'] ?? null) : null,
                'pan_name' => (($formData['has_pan_card'] ?? '') === 'Yes') ? ($formData['hof_pan_name'] ?? null) : null,
                'holds_constitutional_post' => $hasConstitutionalPost,
                'constitutional_post_member_no' => $constitutionalPostDetails,
                'is_registered_gst' => $hasGstReg,
                'gstin' => $gstin,
                'is_child' => false,
                'is_govt_pensioner' => $hasPensioner,
                'govt_pensioner_member_no' => $pensionerDetails,
                'relation_with_head_of_family' => 'Self',
                'applying_for_annapurna_bhandar' => $isHofEligible || (($formData['hof_applying_for_ay'] ?? '') === 'Yes'),
                'has_pan_card' => (($formData['has_pan_card'] ?? '') === 'Yes'),
                'has_three_pucca_rooms' => (($formData['has_pucca_rooms'] ?? '') === 'Yes'),
                'owns_land' => $ownsLand,
                'landholding_size_decimals' => $landSizeDecimals,
                'lgd_district_code' => $lgdDistrictCode,
                'lgd_block_mc_code' => $lgdBlockMcCode,
                'lgd_gp_ward_code' => $lgdGpWardCode,
                'created_by_dist_code' => $createdByDistCode,
                'created_by_local_body_code' => $createdByLocalBodyCode,
            ], 'id');

            // Save HOF Employment Nature
            if (!empty($formData['hof_employment_nature'])) {
                $natures = (array) $formData['hof_employment_nature'];
                foreach ($natures as $nature) {
                    if (!empty($nature)) {
                        DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->insert([
                            'family_member_id' => $hofMemberId,
                            'employment_type' => $nature,
                            'lgd_district_code' => $lgdDistrictCode,
                        ]);
                    }
                }
            }

            // Save HOF DBT Benefits
            if (($formData['hof_has_dbt_benefits'] ?? 'No') === 'Yes' && !empty($formData['hof_dbt_benefits'])) {
                foreach ($formData['hof_dbt_benefits'] as $benefit) {
                    if (!empty($benefit['scheme_name'])) {
                        DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->insert([
                            'family_member_id' => $hofMemberId,
                            'scheme_name' => $benefit['scheme_name'],
                            'opt_out' => (bool) ($benefit['opt_out'] ?? false),
                            'lgd_district_code' => $lgdDistrictCode,
                        ]);
                    }
                }
            }

            // Save HOF Other Credit Cards
            if (!empty($formData['hof_kcc_cards'])) {
                foreach ($formData['hof_kcc_cards'] as $card) {
                    if (!empty($card['type']) && $card['type'] !== 'None') {
                        DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->insert([
                            'family_member_id' => $hofMemberId,
                            'id_type' => $card['type'],
                            'issue_date' => $card['date'] ?? '',
                            'lgd_district_code' => $lgdDistrictCode,
                        ]);
                    }
                }
            }

            // 8. Save Family Members
            foreach ($members as $index => $member) {
                $isChild = (($member['member_type'] ?? 'adult') === 'child');

                $mHasDigitalRationCard = !$isChild && (($member['has_digital_ration_card'] ?? '') === 'Yes');
                $mRationCardNo = $mHasDigitalRationCard ? ($member['ration_card_no'] ?? null) : null;
                $mRationCardType = $mHasDigitalRationCard ? ($member['ration_card_type'] ?? null) : null;

                $mIsEligible = $this->isFemale25to60($member['gender'] ?? '', $member['dob'] ?? '');
                $mApplyingForAY = !$isChild && ($mIsEligible || (($member['applying_for_ay'] ?? 'No') === 'Yes'));
                
                $mBankName = $mApplyingForAY ? ($member['bank_name'] ?? null) : null;
                $mAccNo = $mApplyingForAY ? ($member['acc_no'] ?? null) : null;
                $mIfsc = $mApplyingForAY ? ($member['ifsc'] ?? null) : null;

                $mCaaStatus = $isChild ? 'Not Applicable' : ($member['caa_status'] ?? 'Not Applicable');
                $mCaaAppNo = !$isChild && $mCaaStatus === 'Applied' ? ($member['caa_app_no'] ?? null) : null;
                $mCaaCertNo = !$isChild && $mCaaStatus === 'Issued' ? ($member['caa_cert_no'] ?? null) : null;

                $mSirStatus = $isChild ? 'Not Applicable' : ($member['sir_status'] ?? 'Not Applicable');
                $mSirCaseDetails = !$isChild && $mSirStatus === 'Yes' ? ($member['sir_case_details'] ?? null) : null;

                $mHasHealthInsurance = !$isChild && (($member['has_health_insurance'] ?? '') === 'Yes');
                $mHealthInsuranceType = $mHasHealthInsurance ? ($member['health_insurance_type'] ?? null) : null;
                $mHealthInsurancePremium = ($mHasHealthInsurance && !empty($member['health_insurance_premium'])) ? (float) $member['health_insurance_premium'] : null;
                $mHealthInsuranceSumAssured = ($mHasHealthInsurance && !empty($member['health_insurance_sum_assured'])) ? (float) $member['health_insurance_sum_assured'] : null;

                $memberId = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->insertGetId([
                    'family_id' => $familyId,
                    'is_hof' => false,
                    'member_name' => $member['name'] ?? '',
                    'aadhaar_no' => $member['aadhaar'] ?? '',
                    'mobile_no' => null,
                    'date_of_birth' => !empty($member['dob']) ? $member['dob'] : null,
                    'gender' => !empty($member['gender']) ? $member['gender'] : null,
                    'digital_ration_card_no' => $mRationCardNo,
                    'digital_ration_card_type' => $mRationCardType,
                    'social_category' => $formData['category'] ?? null,
                    'bank_name' => $mBankName,
                    'bank_account_no' => $mAccNo,
                    'ifsc_code' => $mIfsc,
                    'epic_no' => $isChild ? null : (!empty($member['epic_no']) ? $member['epic_no'] : null),
                    'assembly_constituency_no' => $isChild ? null : (!empty($member['assembly_constituency']) ? $member['assembly_constituency'] : null),
                    'part_no' => $isChild ? null : (!empty($member['part_no']) ? $member['part_no'] : null),
                    'caa_application_status' => $mCaaStatus,
                    'caa_application_no' => $mCaaAppNo,
                    'caa_certificate_no' => $mCaaCertNo,
                    'sir2026tribunal_status' => $mSirStatus,
                    'sir2026case_details' => $mSirCaseDetails,
                    'has_four_wheeler' => false,
                    'has_health_insurance' => $mHasHealthInsurance,
                    'health_insurance_type' => $mHealthInsuranceType === 'No' ? null : $mHealthInsuranceType,
                    'health_insurance_sum_assured' => $mHealthInsuranceSumAssured,
                    'health_insurance_annual_premium' => $mHealthInsurancePremium,
                    'literacy_status' => $isChild ? null : (!empty($member['literate_status']) ? $member['literate_status'] : null),
                    'highest_educational_qualifications' => $isChild ? null : (!empty($member['highest_qualification']) ? $member['highest_qualification'] : null),
                    'gross_annual_income' => null,
                    'pays_income_or_professional_tax' => false,
                    'pan_no' => ($isChild || (($member['has_pan_card'] ?? '') !== 'Yes')) ? null : ($member['pan_no'] ?? null),
                    'pan_name' => ($isChild || (($member['has_pan_card'] ?? '') !== 'Yes')) ? null : ($member['pan_name'] ?? null),
                    'holds_constitutional_post' => false,
                    'is_registered_gst' => false,
                    'is_child' => $isChild,
                    'is_govt_pensioner' => false,
                    'relation_with_head_of_family' => !empty($member['relation']) ? $member['relation'] : null,
                    'applying_for_annapurna_bhandar' => $mApplyingForAY,
                    'has_pan_card' => !$isChild && (($member['has_pan_card'] ?? '') === 'Yes'),
                    'lgd_district_code' => $lgdDistrictCode,
                    'lgd_block_mc_code' => $lgdBlockMcCode,
                    'lgd_gp_ward_code' => $lgdGpWardCode,
                    'school_grade' => $isChild ? (!empty($member['school_grade']) ? $member['school_grade'] : null) : null,
                    'school_name' => $isChild ? (!empty($member['school_name']) ? $member['school_name'] : null) : null,
                    'school_type' => $isChild ? (!empty($member['school_type']) ? $member['school_type'] : null) : null,
                    'school_type_other' => $isChild ? (!empty($member['school_type_other']) ? $member['school_type_other'] : null) : null,
                    'vaccination_card_id' => ($isChild && (($member['vaccination_status'] ?? '') === 'Yes' || ($member['vaccination_status'] ?? '') === 'Partial')) ? ($member['vaccination_card_id'] ?? null) : null,
                    'vaccination_status' => $isChild ? (!empty($member['vaccination_status']) ? $member['vaccination_status'] : null) : null,
                    'vaccination_skip_reason_or_date' => ($isChild && (($member['vaccination_status'] ?? '') === 'No' || ($member['vaccination_status'] ?? '') === 'Partial')) ? ($member['vaccination_skip_reason_or_date'] ?? null) : null,
                    'created_by_dist_code' => $createdByDistCode,
                    'created_by_local_body_code' => $createdByLocalBodyCode,
                ], 'id');

                // Save Member Employment Nature
                if (!$isChild && !empty($member['employment_nature'])) {
                    DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->insert([
                        'family_member_id' => $memberId,
                        'employment_type' => $member['employment_nature'],
                        'lgd_district_code' => $lgdDistrictCode,
                    ]);
                }

                // Save Member DBT Benefits
                if (!$isChild && ($member['has_dbt_benefits'] ?? 'No') === 'Yes' && !empty($member['dbt_benefits'])) {
                    foreach ($member['dbt_benefits'] as $benefit) {
                        if (!empty($benefit['scheme_name'])) {
                            DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->insert([
                                'family_member_id' => $memberId,
                                'scheme_name' => $benefit['scheme_name'],
                                'opt_out' => (bool) ($benefit['opt_out'] ?? false),
                                'lgd_district_code' => $lgdDistrictCode,
                            ]);
                        }
                    }
                }

                // Save Member Other Cards
                if (!$isChild && !empty($member['kcc_cards'])) {
                    foreach ($member['kcc_cards'] as $card) {
                        if (!empty($card['type']) && $card['type'] !== 'None') {
                            DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->insert([
                                'family_member_id' => $memberId,
                                'id_type' => $card['type'],
                                'issue_date' => $card['date'] ?? '',
                                'lgd_district_code' => $lgdDistrictCode,
                            ]);
                        }
                    }
                }
            }

            DB::connection('pgsql_annapurna')->commit();

            return [
                'success' => true,
                'familyId' => $familyId,
                'appId' => $appId,
            ];
        } catch (\Exception $e) {
            DB::connection('pgsql_annapurna')->rollBack();
            throw $e;
        }
    }

    /**
     * Calculate age from Date of Birth.
     *
     * @param string|null $dob
     * @return int
     */
    public function getAgeFromDob(?string $dob): int
    {
        if (empty($dob)) {
            return 0;
        }
        try {
            $birthDate = new \DateTime($dob);
            $today = new \DateTime;
            return $today->diff($birthDate)->y;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Check if a person is female and aged 25 to 60.
     *
     * @param string $gender
     * @param string|null $dob
     * @return bool
     */
    public function isFemale25to60(string $gender, ?string $dob): bool
    {
        $age = $this->getAgeFromDob($dob);
        return $gender === 'Female' && $age >= 25 && $age <= 60;
    }
}
