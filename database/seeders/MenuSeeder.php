<?php
// database/seeders/MenuSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Role;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $operatorRole = Role::where('name', 'Operator')->first();
        
        // Dashboard
        $dashboard = Menu::create([
            'name' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'route' => 'dashboard',
            'order' => 1,
            'is_active' => true
        ]);
        
        // Lakshmir Bhandar
        $lbMenu = Menu::create([
            'name' => 'Lakshmir Bhandar',
            'icon' => 'fas fa-hand-holding-heart',
            'order' => 2,
            'is_active' => true
        ]);
        
        Menu::create([
            'name' => 'Application Form',
            'icon' => 'fas fa-file-alt',
            'route' => 'form',
            'parent_id' => $lbMenu->id,
            'order' => 1,
            'is_active' => true,
            'permission_key' => 'canEntry'
        ]);
        
        Menu::create([
            'name' => 'Process Application',
            'icon' => 'fas fa-tasks',
            'route' => 'application-lists',
            'parent_id' => $lbMenu->id,
            'order' => 2,
            'is_active' => true,
            'permission_key' => 'canViewLbApplications'
        ]);
        
        // Assign to roles
        if ($adminRole) {
            $dashboard->roles()->attach($adminRole->id);
            $lbMenu->roles()->attach($adminRole->id);
        }
        
        if ($operatorRole) {
            $dashboard->roles()->attach($operatorRole->id);
            $lbMenu->roles()->attach($operatorRole->id);
        }
    }
}