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
                "scheme_id" => 10,
                "doc_type_id" => 100,
                "is_required" => true,
                "max_file_size" => "100KB",
                "extension_type" => ['jpg', 'jpeg', 'png']
            ),
            array(
                "scheme_id" => 10,
                "doc_type_id" => 101,
                "is_required" => false,
                "max_file_size" => "500KB",
                "extension_type" => ['jpg', 'jpeg', 'png', 'pdf']
            ),
            array(
                "scheme_id" => 10,
                "doc_type_id" => 104,
                "is_required" => true,
                "max_file_size" => "500KB",
                "extension_type" => ['jpg', 'jpeg', 'png', 'pdf']
            ),
            array(
                "scheme_id" => 10,
                "doc_type_id" => 108,
                "is_required" => true,
                "max_file_size" => "500KB",
                "extension_type" => ['jpg', 'jpeg', 'png', 'pdf']
            ),
            array(
                "scheme_id" => 10,
                "doc_type_id" => 109,
                "is_required" => false,
                "max_file_size" => "500KB",
                "extension_type" => ['jpg', 'jpeg', 'png', 'pdf']
            ),
            array(
                "scheme_id" => 10,
                "doc_type_id" => 123,
                "is_required" => true,
                "max_file_size" => "500KB",
                "extension_type" => ['jpg', 'jpeg', 'png', 'pdf']
            )
        );
        foreach ($scheme_attache as $item) {
            SchemeAttachedDocMappings::create([
                'scheme_id'      => $item['scheme_id'],
                'doc_type_id'    => $item['doc_type_id'],
                'is_required'    => $item['is_required'],
                'max_file_size'  => $item['max_file_size'],
                'extension_type' => implode(',', $item['extension_type'])
            ]);
        }
    }
}
