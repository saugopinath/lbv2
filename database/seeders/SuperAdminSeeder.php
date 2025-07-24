<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserPersonal;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
           $rolesArr = ['name' => 'Super Admin', 'guard_name' => 'web', 'created_at' => now(),'updated_at' => now()];
           Role::insert($rolesArr);      
    
        $user = User::create([
            'name' => 'Admin',
            'mobile_no' => '8583035693',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('1234'),
        ]);

        UserPersonal::create([
            'user_id' => $user->id,
            'name' => $user->name,
        ]);
        $role = Role::findByName('Super Admin');
        $user->assignRole($role);
       
    }
}
