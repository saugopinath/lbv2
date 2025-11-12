<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GivePermissionToAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Define the permissions and the roles to which they should be assigned
        $permissions = [
            'RolePermissionManagement',
            'UserManagement',
            'DutyAssignManagement',
            'OfficeManagement',
            'CasteManagementPermission',
            'ViewCastApplication',
            'TakeActionForCaste',
            'VerifyCasteApplication',
            'ApproveCasteApplication',
            'RevertCasteApplication',
            'CastModifyRequest',
            'EditRevertApplication',

        ];


        DB::table('public.model_has_permissions');
    }
}
