<?php

namespace Database\Seeders\Role;

use App\Models\Role;
use App\Models\User;
use App\Models\UserPersonal;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $c_time=Carbon::now()->format('Y/m/d H:i:s');
        $password_expires_at= Carbon::now()->addDays(intval(Config::get('app.password_expire_day')))->format('Y/m/d H:i:s');
        $rolesArr = ['name' => 'Super Admin', 'guard_name' => 'web', 'created_at' => now(),'updated_at' => now()];
        $user = User::create([
            'name' => 'Admin',
            'mobile_no' => '8583035693',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('1234'),
            'password_set_time' => $c_time, 
            'password_expires_at' => $password_expires_at, 
        ]);

        UserPersonal::create([
            'user_id' => $user->id,
            'name' => $user->name,
        ]);
        $role = Role::findByName('Super Admin');
        $user->assignRole($role);
       
    }
}
