<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Codemaster;
class CmoAtrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cmomasterParents = array(
            array(
                "name" => "CMO ATR",
                "short_name" => "cmo_atr",
                "code" => "25",
            ),
        );
        foreach ($cmomasterParents as $cmomasterParent_item) {
            Codemaster::create([
                'name'     => strtoupper($cmomasterParent_item['name']),
                'code'     => $cmomasterParent_item['code'],
                'short_name'     => $cmomasterParent_item['short_name'],
            ]);
        }
        $codemasterChilds = array(
            array(
                "name" => "Beyond the administrative jurisdiction of this Office/ Deptt.",
                "short_name" => "beyond_the_administrative_jurisdiction_of_this_office/_deptt.",
                "parent_short_code" => "cmo_atr",
                "code" => "250",
            ),
            array(
                "name" => "Repeated Complaint/already redressed",
                "short_name" => "repeated_complaint/already_redressed",
                "parent_short_code" => "cmo_atr",
                "code" => "251",
            ),
            array(
                "name" => "Benefit/Service provided",
                "short_name" => "benefit/service_provided",
                "parent_short_code" => "cmo_atr",
                "code" => "252",
            ),
            array(
                "name" => "Matter taken up for expected resolution within 90 days",
                "short_name" => "matter_taken_up_for_expected_resolution_within_90_days",
                "parent_short_code" => "cmo_atr",
                "code" => "253",
            ),
            array(
                "name" => "Pending for policy decision at department level",
                "short_name" => "pending_for_policy_decision_at_department_level",
                "parent_short_code" => "cmo_atr",
                "code" => "254",
            ),
            array(
                "name" => "Matter taken up for expected resolution beyond 90 days",
                "short_name" => "matter_taken_up_for_expected_resolution_beyond_90_days",
                "parent_short_code" => "cmo_atr",
                "code" => "255",
            ),
            array(
                "name" => "Complaint not sustained/not genuine/not admissible",
                "short_name" => "complaint_not_sustained/not_genuine/not_admissible",
                "parent_short_code" => "cmo_atr",
                "code" => "256",
            ),
            array(
                "name" => "Subjudice Case",
                "short_name" => "subjudice_case",
                "parent_short_code" => "cmo_atr",
                "code" => "257",
            ),
            array(
                "name" => "Not eligible to get benefit/ service",
                "short_name" => "not_eligible_to_get_benefit/_service",
                "parent_short_code" => "cmo_atr",
                "code" => "258",
            ),
            array(
                "name" => "Complainant not found / unable or not willing to share information",
                "short_name" => "complainant_not_found_/_unable_or_not_willing_to_share_information",
                "parent_short_code" => "cmo_atr",
                "code" => "259",
            ),
            array(
                "name" => "Beyond State Govt. purview",
                "short_name" => "beyond_state_govt._purview",
                "parent_short_code" => "cmo_atr",
                "code" => "260",
            ),
            array(
                "name" => "Employment Prayer - processed/information provided",
                "short_name" => "employment_prayer_-_processed/information_provided",
                "parent_short_code" => "cmo_atr",
                "code" => "261",
            ),
        );
        foreach ($codemasterChilds as $codemasterChild_item) {
            Codemaster::create([
                'name' => strtoupper($codemasterChild_item['name']),
                'code' => $codemasterChild_item['code'],
                'parent_short_code' => $codemasterChild_item['parent_short_code'],
                'short_name'     => $codemasterChild_item['short_name'],
                'parent_id'   => Codemaster::where('short_name', $codemasterChild_item['parent_short_code'])->firstOrFail()->id,
            ]);
        }
    }
}
