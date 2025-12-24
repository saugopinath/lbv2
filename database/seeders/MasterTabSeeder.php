<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterTab;

class MasterTabSeeder extends Seeder
{
    public function run(): void
    {
        $tabs = [
            [
                'tab_code' => 'persona',
                'tab_name' => 'Persona',
                'tab_key' => 'persona_key',
                'tab_component' => 'PersonaComponent',
                'is_active' => true,
            ],
            [
                'tab_code' => 'contact',
                'tab_name' => 'Contact',
                'tab_key' => 'contact_key',
                'tab_component' => 'ContactComponent',
                'is_active' => true,
            ],
            [
                'tab_code' => 'bank',
                'tab_name' => 'Bank Details',
                'tab_key' => 'bank_key',
                'tab_component' => 'BankComponent',
                'is_active' => true,
            ],
            [
                'tab_code' => 'encloser',
                'tab_name' => 'Enclosure',
                'tab_key' => 'encloser_key',
                'tab_component' => 'EnclosureComponent',
                'is_active' => true,
            ],
        ];

        foreach ($tabs as $tab) {
            MasterTab::updateOrCreate(
                ['tab_code' => $tab['tab_code']],
                $tab
            );
        }
    }
}
