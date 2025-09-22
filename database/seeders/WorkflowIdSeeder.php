<?php

namespace Database\Seeders;

use App\Models\Codemaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkflowIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codemasterParents = array(
            array(
                "name" => "Work Flow OP Type",
                "short_name" => "work_flow_op_type",
                "code" => "230",
            ),

        );
        foreach ($codemasterParents as $codemasterParent_item) {
            Codemaster::create([
                'name'     => strtoupper($codemasterParent_item['name']),
                'code'     => $codemasterParent_item['code'],
                'short_name'     => $codemasterParent_item['short_name'],
            ]);
        }
        $codemasterChilds = array(

            array(
                "name" => "WORK FLOW ENTRY",
                "short_name" => "work_flow_entry",
                "parent_short_code" => "work_flow_op_type",
                "code" => "2301",
            ),
            array(
                "name" => "WORK FLOW VERIFY",
                "short_name" => "work_flow_verify",
                "parent_short_code" => "work_flow_op_type",
                "code" => "2302",
            ),
            array(
                "name" => "WORK FLOW APPROVE",
                "short_name" => "work_flow_approve",
                "parent_short_code" => "work_flow_op_type",
                "code" => "2303",
            ),
            array(
                "name" => "WORK FLOW REVERT",
                "short_name" => "work_flow_revert",
                "parent_short_code" => "work_flow_op_type",
                "code" => "2304",
            ),
            array(
                "name" => "WORK FLOW REJECT",
                "short_name" => "work_flow_reject",
                "parent_short_code" => "work_flow_op_type",
                "code" => "2305",
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
