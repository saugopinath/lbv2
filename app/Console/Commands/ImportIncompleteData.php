<?php

namespace App\Console\Commands;

use App\Models\ApplicantIncompletDeatil;
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
            ->where('scheme_id', 20)
            ->where('next_level_role_id', 2)
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

        $insertData = [];

        foreach ($beneficiaries as $beneficiary) {

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

        ApplicantIncompletDeatil::insert($insertData);

        $this->info("Inserted {$beneficiaries->count()} Records For Scheme : {$scheme_id}");
    }


    public function importNoMobile($scheme_id)
    {
        $incomplete_id = 141;
        $approve_id = $this->approveRoleId($scheme_id);
        if (!$approve_id) {
            $this->warn("No Approve Role Found For Scheme : {$scheme_id}");
            return;
        }
        $beneficiaries = BeneficiaryPersonalDetail::query()
            ->where('scheme_id', 20)
            ->where('next_level_role_id', 2)

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

        $insertData = [];

        foreach ($beneficiaries as $beneficiary) {

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

        ApplicantIncompletDeatil::insert($insertData);

        $this->info("Inserted {$beneficiaries->count()} Records For Scheme : {$scheme_id}");
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