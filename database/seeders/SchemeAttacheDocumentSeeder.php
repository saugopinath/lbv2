<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SchemeAttachedDocMappings;
use App\Models\Codemaster;
class SchemeAttacheDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scheme_attache = array(
            
            array(      
                "scheme_id" => "20",
                "doc_type_id" => "117",
                "is_required" => true
            ),
            array(      
                    "scheme_id" => "20",
                    "doc_type_id" => "2",
                    "is_required" => true
            ),
            array(      
                "scheme_id" => "20",
                "doc_type_id" => "6",
                "is_required" => true
            ),
            array(      
                "scheme_id" => "20",
                "doc_type_id" => "10",
                "is_required" => true
            ),
            array(      
                "scheme_id" => "20",
                "doc_type_id" => "3",
                "is_required" => false
            ),
            array(      
                "scheme_id" => "20",
                "doc_type_id" => "11",
                "is_required" => true
                )
            
        );
        foreach ($scheme_attache as $item) {
            SchemeAttachedDocMappings::create([
                'scheme_id'     => $item['scheme_id'],
                'doc_type_id'     => $item['doc_type_id'],
                'is_required'     => $item['is_required']
            ]);
        }
    }
}
