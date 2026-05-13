<?php

namespace App\Console\Commands;

use App\Models\ApplicantIncompletDeatil;
use App\Models\BeneficiaryAadhaar;
use App\Models\BeneficiaryBank;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Scheme;
use App\Models\WorkflowsteproleMapping;
use Illuminate\Console\Command;

class ImportIncompleteData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-incomplete-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Incomplete Data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schemes = [20];

        $this->info('Started Importing Incomplete Data -- NO AADHAAR -- ' . now());

        foreach ($schemes as $scheme_id) {

            $this->info("Processing Scheme ID : {$scheme_id}");

            $this->importNoAadhar($scheme_id);

            $this->info("Completed Scheme ID : {$scheme_id}");
        }

        $this->info('Finished Importing Incomplete Data -- NO AADHAAR -- ' . now());

        $this->info('Started Importing Incomplete Data -- NO MOBILE -- ' . now());

        foreach ($schemes as $scheme_id) {

            $this->info("Processing Scheme ID : {$scheme_id}");

            $this->importNoMobile($scheme_id);

            $this->info("Completed Scheme ID : {$scheme_id}");
        }

        $this->info('Finished Importing Incomplete Data -- NO MOBILE -- ' . now());

        $this->info('Started Importing Incomplete Data -- DUP AADHAAR -- ' . now());

        foreach ($schemes as $scheme_id) {

            $this->info("Processing Scheme ID : {$scheme_id}");

            $this->importDuplicateAadhar($scheme_id);

            $this->info("Completed Scheme ID : {$scheme_id}");
        }

        $this->info('Finished Importing Incomplete Data -- DUP AADHAAR -- ' . now());

        $this->info('Started Importing Incomplete Data -- DUP MOBILE -- ' . now());

        foreach ($schemes as $scheme_id) {

            $this->info("Processing Scheme ID : {$scheme_id}");

            $this->importDuplicateMobile($scheme_id);

            $this->info("Completed Scheme ID : {$scheme_id}");
        }

        $this->info('Finished Importing Incomplete Data -- DUP MOBILE -- ' . now());

        $this->info('Started Importing Incomplete Data -- DUP BANK A/C -- ' . now());

        foreach ($schemes as $scheme_id) {

            $this->info("Processing Scheme ID : {$scheme_id}");

            $this->importDuplicateBank($scheme_id);

            $this->info("Completed Scheme ID : {$scheme_id}");
        }

        $this->info('Finished Importing Incomplete Data -- DUP BANK A/C -- ' . now());
    }

    /**
     * Import beneficiaries having no aadhaar
     */
    public function importNoAadhar($scheme_id)
    {
        $incomplete_id = 141;
        $approve_id = $this->approveRoleId($scheme_id);
        if (!$approve_id) {
            $this->warn("No Approve Role Found For Scheme : {$scheme_id}");
            return;
        }
        $beneficiaries = BeneficiaryPersonalDetail::query()
            ->where('scheme_id', $scheme_id)
            ->where('next_level_role_id', $approve_id)
            ->whereHas('aadhar', function ($q) {
                $q->whereNull('encoded_aadhar');
            })
            ->select([
                'application_id',
                'beneficiary_id',
                'scheme_id',
            ])
            ->get();

        if ($beneficiaries->isEmpty()) {
            $this->info("No Incomplete Aadhaar Data Found For Scheme : {$scheme_id}");
            return;
        }

        $existingBeneficiaryIds = ApplicantIncompletDeatil::where('scheme_id', $scheme_id)
            ->where('incomplet_type', $incomplete_id)
            ->whereNull('next_level_request_id')
            ->whereIn('beneficiary_id', $beneficiaries->pluck('beneficiary_id'))
            ->pluck('beneficiary_id')
            ->toArray();

        $insertData = [];

        foreach ($beneficiaries as $beneficiary) {
            if (in_array($beneficiary->beneficiary_id, $existingBeneficiaryIds)) {
                continue;
            }

            $insertData[] = [
                'scheme_id' => $scheme_id,
                'application_id' => $beneficiary->application_id,
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'incomplet_type' => $incomplete_id,
                'next_level_request_id' => null,
                'old_value' => json_encode([
                    'aadhaar' => null,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            ApplicantIncompletDeatil::insert($insertData);
            $this->info("Inserted " . count($insertData) . " Records For Scheme : {$scheme_id}");
        } else {
            $this->info("No New Records To Insert For Scheme : {$scheme_id}");
        }
    }


    public function importNoMobile($scheme_id)
    {
        $incomplete_id = 142;
        $approve_id = $this->approveRoleId($scheme_id);
        if (!$approve_id) {
            $this->warn("No Approve Role Found For Scheme : {$scheme_id}");
            return;
        }
        $beneficiaries = BeneficiaryPersonalDetail::query()
            ->where('scheme_id', $scheme_id)
            ->where('next_level_role_id', $approve_id)
            ->where(function ($q) {
                $q->whereNull('other_details->>mobile_no')
                    ->orWhere('other_details->>mobile_no', '')
                    ->orWhere('other_details->>mobile_no', 'N/A')
                    ->orWhere('other_details->>mobile_no', 'n/a')
                    ->orWhere('other_details->>mobile_no', '0')
                    ->orWhere('other_details->>mobile_no', '0000000000')
                    ->orWhere('other_details->>mobile_no', '1234567890');
            })
            ->select([
                'application_id',
                'beneficiary_id',
                'scheme_id',
            ])
            ->get();

        if ($beneficiaries->isEmpty()) {
            $this->info("No Incomplete Mobile Data Found For Scheme : {$scheme_id}");
            return;
        }

        $existingBeneficiaryIds = ApplicantIncompletDeatil::where('scheme_id', $scheme_id)
            ->where('incomplet_type', $incomplete_id)
            ->whereNull('next_level_request_id')
            ->whereIn('beneficiary_id', $beneficiaries->pluck('beneficiary_id'))
            ->pluck('beneficiary_id')
            ->toArray();

        $insertData = [];

        foreach ($beneficiaries as $beneficiary) {
            if (in_array($beneficiary->beneficiary_id, $existingBeneficiaryIds)) {
                continue;
            }

            $insertData[] = [
                'scheme_id' => $scheme_id,
                'application_id' => $beneficiary->application_id,
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'incomplet_type' => $incomplete_id,
                'next_level_request_id' => null,
                'old_value' => json_encode([
                    'mobile_no' => null,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            ApplicantIncompletDeatil::insert($insertData);
            $this->info("Inserted " . count($insertData) . " Records For Scheme : {$scheme_id}");
        } else {
            $this->info("No New Records To Insert For Scheme : {$scheme_id}");
        }
    }

    public function importDuplicateMobile($scheme_id)
    {
        $incomplete_id = 1410; // Duplicate Mobile Incomplete ID

        $approve_id = $this->approveRoleId($scheme_id);

        if (!$approve_id) {
            $this->warn("No Approve Role Found For Scheme : {$scheme_id}");
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Find Duplicate Mobile Numbers
        |--------------------------------------------------------------------------
        */
        $duplicateMobiles = BeneficiaryPersonalDetail::query()
            ->where('scheme_id', $scheme_id)
            ->where('next_level_role_id', $approve_id)
            ->whereNotNull('other_details->>mobile_no')
            ->whereNotIn('other_details->>mobile_no', [
                '',
                '0',
                '0000000000',
                '1234567890',
                'N/A',
                'n/a'
            ])
            ->selectRaw("
            other_details->>'mobile_no' as mobile_no,
            COUNT(*) as total
        ")
            ->groupBy('mobile_no')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('mobile_no');

        if ($duplicateMobiles->isEmpty()) {
            $this->info("No Duplicate Mobile Found For Scheme : {$scheme_id}");
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch Beneficiaries Having Duplicate Mobile Numbers
        |--------------------------------------------------------------------------
        */
        $beneficiaries = BeneficiaryPersonalDetail::query()
            ->where('scheme_id', $scheme_id)
            ->where('next_level_role_id', $approve_id)
            ->whereIn('other_details->>mobile_no', $duplicateMobiles)
            ->select([
                'application_id',
                'beneficiary_id',
                'scheme_id',
                'other_details'
            ])
            ->get();

        if ($beneficiaries->isEmpty()) {
            $this->info("No Duplicate Mobile Beneficiaries Found");
            return;
        }

        $existingBeneficiaryIds = ApplicantIncompletDeatil::where('scheme_id', $scheme_id)
            ->where('incomplet_type', $incomplete_id)
            ->whereNull('next_level_request_id')
            ->whereIn('beneficiary_id', $beneficiaries->pluck('beneficiary_id'))
            ->pluck('beneficiary_id')
            ->toArray();

        $insertData = [];

        foreach ($beneficiaries as $beneficiary) {
            if (in_array($beneficiary->beneficiary_id, $existingBeneficiaryIds)) {
                continue;
            }

            $mobile_no = data_get($beneficiary->other_details, 'mobile_no');

            $insertData[] = [
                'scheme_id' => $scheme_id,
                'application_id' => $beneficiary->application_id,
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'incomplet_type' => $incomplete_id,
                'next_level_request_id' => null,
                'old_value' => json_encode([
                    'mobile_no' => $mobile_no,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            ApplicantIncompletDeatil::insert($insertData);
            $this->info("Inserted " . count($insertData) . " Duplicate Mobile Records For Scheme : {$scheme_id}");
        } else {
            $this->info("No New Duplicate Mobile Records To Insert For Scheme : {$scheme_id}");
        }
    }

    public function importDuplicateAadhar($scheme_id)
    {
        $incomplete_id = 149; // Duplicate Aadhaar Incomplete ID

        $approve_id = $this->approveRoleId($scheme_id);

        if (!$approve_id) {
            $this->warn("No Approve Role Found For Scheme : {$scheme_id}");
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Find Duplicate Aadhaar Hashes
        |--------------------------------------------------------------------------
        */
        $duplicateAadharHashes = BeneficiaryAadhaar::query()
            ->whereNotNull('aadhar_hash')
            ->where('aadhar_hash', '!=', '')
            ->selectRaw('aadhar_hash, COUNT(*) as total')
            ->groupBy('aadhar_hash')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('aadhar_hash');

        if ($duplicateAadharHashes->isEmpty()) {
            $this->info("No Duplicate Aadhaar Found For Scheme : {$scheme_id}");
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch Beneficiaries Having Duplicate Aadhaar
        |--------------------------------------------------------------------------
        */
        $beneficiaries = BeneficiaryPersonalDetail::query()
            ->where('scheme_id', $scheme_id)
            ->where('next_level_role_id', $approve_id)
            ->whereHas('aadhar', function ($q) use ($duplicateAadharHashes) {
                $q->whereIn('aadhar_hash', $duplicateAadharHashes);
            })
            ->with('aadhar:id,beneficiary_id,aadhar_hash')
            ->select([
                'application_id',
                'beneficiary_id',
                'scheme_id',
            ])
            ->get();

        if ($beneficiaries->isEmpty()) {
            $this->info("No Duplicate Aadhaar Beneficiaries Found");
            return;
        }

        $existingBeneficiaryIds = ApplicantIncompletDeatil::where('scheme_id', $scheme_id)
            ->where('incomplet_type', $incomplete_id)
            ->whereNull('next_level_request_id')
            ->whereIn('beneficiary_id', $beneficiaries->pluck('beneficiary_id'))
            ->pluck('beneficiary_id')
            ->toArray();

        $insertData = [];

        foreach ($beneficiaries as $beneficiary) {
            if (in_array($beneficiary->beneficiary_id, $existingBeneficiaryIds)) {
                continue;
            }

            $aadhar_hash = optional($beneficiary->aadhar)->aadhar_hash;

            $insertData[] = [
                'scheme_id' => $scheme_id,
                'application_id' => $beneficiary->application_id,
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'incomplet_type' => $incomplete_id,
                'next_level_request_id' => null,
                'old_value' => json_encode([
                    'aadhar_hash' => $aadhar_hash,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            ApplicantIncompletDeatil::insert($insertData);
            $this->info("Inserted " . count($insertData) . " Duplicate Aadhaar Records For Scheme : {$scheme_id}");
        } else {
            $this->info("No New Duplicate Aadhaar Records To Insert For Scheme : {$scheme_id}");
        }
    }

    public function importDuplicateBank($scheme_id)
    {
        $incomplete_id = 1411; // Duplicate Bank Incomplete ID

        $approve_id = $this->approveRoleId($scheme_id);

        if (!$approve_id) {
            $this->warn("No Approve Role Found For Scheme : {$scheme_id}");
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Find Duplicate Bank Account Numbers
        |--------------------------------------------------------------------------
        */
        $duplicateBankAccounts = BeneficiaryBank::query()
            ->whereNotNull('bank_account_number')
            ->where('bank_account_number', '!=', '')
            ->whereNotIn('bank_account_number', [
                '0',
                '0000000000',
                'N/A',
                'n/a'
            ])
            ->selectRaw('bank_account_number, COUNT(*) as total')
            ->groupBy('bank_account_number')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('bank_account_number');

        if ($duplicateBankAccounts->isEmpty()) {
            $this->info("No Duplicate Bank Account Found For Scheme : {$scheme_id}");
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch Beneficiaries Having Duplicate Bank Accounts
        |--------------------------------------------------------------------------
        */
        $beneficiaries = BeneficiaryPersonalDetail::query()
            ->where('scheme_id', $scheme_id)
            ->where('next_level_role_id', $approve_id)
            ->whereHas('bank', function ($q) use ($duplicateBankAccounts) {
                $q->whereIn('bank_account_number', $duplicateBankAccounts);
            })
            ->with([
                'bank:id,application_id,beneficiary_id,bank_account_number'
            ])
            ->select([
                'application_id',
                'beneficiary_id',
                'scheme_id',
            ])
            ->get();

        if ($beneficiaries->isEmpty()) {
            $this->info("No Duplicate Bank Beneficiaries Found");
            return;
        }

        $existingBeneficiaryIds = ApplicantIncompletDeatil::where('scheme_id', $scheme_id)
            ->where('incomplet_type', $incomplete_id)
            ->whereNull('next_level_request_id')
            ->whereIn('beneficiary_id', $beneficiaries->pluck('beneficiary_id'))
            ->pluck('beneficiary_id')
            ->toArray();

        $insertData = [];

        foreach ($beneficiaries as $beneficiary) {
            if (in_array($beneficiary->beneficiary_id, $existingBeneficiaryIds)) {
                continue;
            }

            $bank_account_number = optional($beneficiary->bank)->bank_account_number;

            $insertData[] = [
                'scheme_id' => $scheme_id,
                'application_id' => $beneficiary->application_id,
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'incomplet_type' => $incomplete_id,
                'next_level_request_id' => null,
                'old_value' => json_encode([
                    'bank_account_number' => $bank_account_number,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            ApplicantIncompletDeatil::insert($insertData);
            $this->info("Inserted " . count($insertData) . " Duplicate Bank Records For Scheme : {$scheme_id}");
        } else {
            $this->info("No New Duplicate Bank Records To Insert For Scheme : {$scheme_id}");
        }
    }

    public function approveRoleId($scheme_id)
    {
        $role = WorkflowsteproleMapping::where('scheme_id', $scheme_id)
            ->where('is_final_step', true)
            ->whereNull('module_id')
            ->first();

        return $role?->next_label_role_id;
    }
}