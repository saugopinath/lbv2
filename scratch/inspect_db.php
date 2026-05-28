<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

try {
    DB::beginTransaction();

    $applicationId = DB::table('lb_scheme.unique_app_ben_ids')->insertGetId([
        'scheme_id' => 21,
        'created_at' => now(),
        'updated_at' => now()
    ], 'application_id');
    
    $uniqueRow = DB::table('lb_scheme.unique_app_ben_ids')->where('application_id', $applicationId)->first();
    $beneficiaryId = $uniqueRow->beneficiary_id;

    echo "Generated Application ID: " . $applicationId . "\n";
    echo "Generated Beneficiary ID: " . $beneficiaryId . "\n";

    // Insert details
    DB::table('lb_scheme.annapurna_yojana_family_details')->insert([
        'scheme_id' => 21,
        'application_id' => $applicationId,
        'beneficiary_id' => $beneficiaryId,
        'application_type' => 'New',
        'application_date' => now()->format('Y-m-d'),
        'applicant_name' => 'John Doe Test',
        'dob' => '1980-01-01',
        'age' => Carbon::parse('1980-01-01')->age,
        'gender' => 'Male',
        'caste' => 19,
        'aadhaar_no' => '123456789012',
        'mobile_no' => '9876543210',
        'district_id' => 1,
        'rural_urban' => 2,
        'blockurban' => 1,
        'gpward' => 1,
        'pincode' => '700001',
        'bank_name' => 'SBI',
        'ifsc_code' => 'SBIN0000001',
        'account_no' => '1234567890',
        'other_details' => json_encode(['test' => true]),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Inserted family details successfully.\n";

    // Insert member
    DB::table('lb_scheme.annapurna_yojana_family_members')->insert([
        'application_id' => $applicationId,
        'member_name' => 'Jane Doe Test',
        'member_dob' => '2010-05-15',
        'member_age' => Carbon::parse('2010-05-15')->age,
        'member_gender' => 'Female',
        'member_relation' => 'Daughter',
        'member_aadhaar' => '987654321012',
        'is_disabled' => 0,
        'is_student' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Inserted family member successfully.\n";

    DB::commit();
    echo "Transaction committed successfully!\n";

    // Clean up test data
    DB::table('lb_scheme.annapurna_yojana_family_members')->where('application_id', $applicationId)->delete();
    DB::table('lb_scheme.annapurna_yojana_family_details')->where('application_id', $applicationId)->delete();
    DB::table('lb_scheme.unique_app_ben_ids')->where('application_id', $applicationId)->delete();
    echo "Cleaned up test data successfully!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
