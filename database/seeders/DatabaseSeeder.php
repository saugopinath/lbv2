<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $this->call([
            LGD\StateSeeder::class,
            LGD\DistrictSeeder::class,
            LGD\BlockSeeder::class,
            LGD\PanchayatSeeder::class,
            LGD\SubdivisionSeeder::class,
            LGD\MunicipalitiesSeeder::class,
            LGD\WardSeeder::class,
            // BankSeeder::class,
            // CodemasterSeeder::class,
            // DepartmentSeeder::class,
            // IfscSeeder::class,
            // SchemeSeeder::class,
            MasterMimeTypeSeeder::class,
            // SchemeAttacheDocumentSeeder::class,
            // RolePermissionSeeder::class,
            // SuperAdminSeeder::class,
            // DraftApplicantSeeder::class,
            // BeneficiaryApplicantSeeder::class,
            // BenRejectDetailsSeeder::class,
            Bank\BankSeeder::class,
            Bank\IfscSeeder::class,
            DepartmentSeeder::class,
            CodemasterSeeder::class,
            SchemeSeeder::class,
            Role\RolePermissionSeeder::class,
            OfficeMaster\RoleOfficeTypeSeeder::class,
            OfficeMaster\OfficeMastersTableSeeder::class,
            Role\SuperAdminSeeder::class,
            Role\WbHodSeeder::class,
            Role\PaschimMedinipurApproverSeeder::class,
            Role\DaspurIIBlockVerifierSeeder::class,
            Role\DaspurIIBlockOperatorSeeder::class,
            Role\GhatalSdoOperatorSeeder::class,
            Role\GhatalSdoVerifierSeeder::class,
        ]);
    }
}
