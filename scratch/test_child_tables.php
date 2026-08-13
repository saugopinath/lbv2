<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Livewire\AnnapurnaYojanaForm;
use Illuminate\Support\Facades\DB;

DB::connection('pgsql_annapurna')->beginTransaction();

try {
    echo "Starting child tables stability verification...\n";

    $form = new AnnapurnaYojanaForm();
    $form->mount(21, 'Annapurna Yojana', null);

    // Minimal form data
    $form->formData['hof_name'] = 'Test HOF Child Tables';
    $form->formData['contact_no'] = '9999988888';
    $form->formData['hof_dob'] = '1985-02-02';
    $form->formData['hof_gender'] = 'Female';
    $form->formData['district_id'] = '19';
    $form->formData['rural_urban'] = '2';
    $form->formData['blockurban'] = '340';
    $form->formData['gpward'] = '3170';
    $form->formData['village_town'] = 'Test Village';
    $form->formData['police_station'] = 'Test PS';
    $form->formData['post_office'] = 'Test PO';
    $form->formData['pincode'] = '721401';
    $form->formData['hof_bank_name'] = 'State Bank of India';
    $form->formData['hof_acc_no'] = '123456789012';
    $form->formData['hof_ifsc'] = 'SBIN0001234';
    $form->formData['category'] = 'SC';
    $form->formData['caste_certificate_no'] = 'SC12345';
    $form->formData['owns_4_wheeler'] = 'No';
    $form->formData['owns_land'] = 'No';
    $form->formData['has_pucca_rooms'] = 'No';
    $form->formData['has_health_insurance'] = 'No';
    $form->formData['pays_tax'] = 'No';
    $form->formData['total_annual_income'] = '50000';
    $form->formData['agree_consent'] = true;

    // HOF child table properties
    $form->formData['hof_employment_nature'] = ['Agriculture', 'Business'];
    $form->formData['hof_has_dbt_benefits'] = 'Yes';
    $form->formData['hof_dbt_benefits'] = [
        ['scheme_name' => 'Lakshmir Bhandar', 'opt_out' => false],
        ['scheme_name' => 'Khadya Sathi', 'opt_out' => true]
    ];
    $form->formData['hof_kcc_cards'] = [
        ['type' => 'KCC', 'date' => '2020-01-01']
    ];

    // Save draft 1
    echo "\n--- First Save Draft ---\n";
    $form->saveDraft();

    $familyId = $form->familyId;
    $hofDb = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
        ->where('family_id', $familyId)
        ->where('is_hof', true)
        ->first();
    $hofMemberId = $hofDb->id;
    echo "Family ID: $familyId, HOF Member ID: $hofMemberId\n";

    // Count records in child tables
    $naturesCount1 = DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->where('family_member_id', $hofMemberId)->where('is_deleted', 0)->count();
    $schemesCount1 = DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->where('family_member_id', $hofMemberId)->where('is_deleted', 0)->count();
    $otherIdsCount1 = DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->where('family_member_id', $hofMemberId)->where('is_deleted', 0)->count();

    echo "Initial active records count:\n";
    echo "- Natures: $naturesCount1 (Expected: 2)\n";
    echo "- Schemes: $schemesCount1 (Expected: 2)\n";
    echo "- Other IDs: $otherIdsCount1 (Expected: 1)\n";

    if ($naturesCount1 !== 2 || $schemesCount1 !== 2 || $otherIdsCount1 !== 1) {
        throw new Exception("Initial counts do not match expected values!");
    }

    // Save draft 2 (No changes)
    echo "\n--- Second Save Draft (No changes) ---\n";
    $form->saveDraft();

    // Check total records (including deleted ones)
    $totalNatures = DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->where('family_member_id', $hofMemberId)->count();
    $totalSchemes = DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->where('family_member_id', $hofMemberId)->count();
    $totalOtherIds = DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->where('family_member_id', $hofMemberId)->count();

    echo "Total records (active + deleted) after second save:\n";
    echo "- Total Natures: $totalNatures (Expected: 2 if no deletion occurred)\n";
    echo "- Total Schemes: $totalSchemes (Expected: 2 if no deletion occurred)\n";
    echo "- Total Other IDs: $totalOtherIds (Expected: 1 if no deletion occurred)\n";

    if ($totalNatures > 2 || $totalSchemes > 2 || $totalOtherIds > 1) {
        throw new Exception("New records were inserted/deleted on an unchanged save!");
    }

    // Modify: remove a scheme, update an opt_out, add a new other ID
    echo "\n--- Modifying HOF properties ---\n";
    // Remove "Khadya Sathi", keep "Lakshmir Bhandar" but change opt_out to true
    $form->formData['hof_dbt_benefits'] = [
        ['scheme_name' => 'Lakshmir Bhandar', 'opt_out' => true]
    ];
    // Add new KCC card of type "Student Credit Card"
    $form->formData['hof_kcc_cards'][] = ['type' => 'Student Credit Card', 'date' => '2022-02-02'];

    echo "\n--- Third Save Draft (Modifications) ---\n";
    $form->saveDraft();

    // Verify active counts
    $naturesCount3 = DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->where('family_member_id', $hofMemberId)->where('is_deleted', 0)->count();
    $schemesCount3 = DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->where('family_member_id', $hofMemberId)->where('is_deleted', 0)->count();
    $otherIdsCount3 = DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->where('family_member_id', $hofMemberId)->where('is_deleted', 0)->count();

    echo "Active counts after third save:\n";
    echo "- Natures: $naturesCount3 (Expected: 2)\n";
    echo "- Schemes: $schemesCount3 (Expected: 1)\n";
    echo "- Other IDs: $otherIdsCount3 (Expected: 2)\n";

    // Verify soft deleted ones in DB
    $deletedSchemes = DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')
        ->where('family_member_id', $hofMemberId)
        ->where('scheme_name', 'Khadya Sathi')
        ->first();
    echo "Khadya Sathi is_deleted in DB: " . ($deletedSchemes->is_deleted ?? 'NULL') . "\n";
    if (!$deletedSchemes || $deletedSchemes->is_deleted != 1) {
        throw new Exception("Khadya Sathi was not soft-deleted!");
    }

    // Verify updated opt_out
    $keptScheme = DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')
        ->where('family_member_id', $hofMemberId)
        ->where('scheme_name', 'Lakshmir Bhandar')
        ->where('is_deleted', 0)
        ->first();
    echo "Lakshmir Bhandar opt_out: " . ($keptScheme->opt_out ? 'true' : 'false') . " (Expected: true)\n";
    if (!$keptScheme || !$keptScheme->opt_out) {
        throw new Exception("Lakshmir Bhandar opt_out was not updated!");
    }

    echo "\n🎉 SUCCESS: Stability verification script compiled correctly.\n";
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
} finally {
    DB::connection('pgsql_annapurna')->rollBack();
}
