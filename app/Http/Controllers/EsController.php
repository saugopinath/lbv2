<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ElasticsearchService;
use App\Models\Scheme;
use App\Models\OfficeMaster;
use App\Models\UserRoleSchemeOfficeMapping;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPersonal;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
class EsController extends Controller
{
    public function __construct(
        
        protected ElasticsearchService $elasticsearchService,
      
    ) {
    }
  
  

    public function test()
    {
        dd($this->elasticsearchService->testConnection());
       
    }
    public function push()
    {
        $c_time=Carbon::now()->format('Y/m/d H:i:s');
        $password_expires_at= Carbon::now()->addDays(intval(Config::get('app.password_expire_day')))->format('Y/m/d H:i:s');
        $role_hod = Role::findByName('HOD');
        $office = OfficeMaster::where('office_type_id',151)->first();
        $scheme = Scheme::where('short_name','LB')->first();
    
        $user_hod = User::create([
            'name' => 'wbhod1',
            'mobile_no' => '9733596960',
            'email' => 'wbhod1@gmail.com',
            'password' => Hash::make('1234'),
            'password_set_time' => $c_time, 
            'password_expires_at' => $password_expires_at, 
        ]);

        UserPersonal::create([
            'user_id' => $user_hod->id,
            'name' => $user_hod->name,
        ]);
        $user_hod->assignRole($role_hod);
        $user_office = UserRoleSchemeOfficeMapping::create([
            'user_id' =>  $user_hod->id,
            'scheme_id' => $scheme->id,
            'role_id' => $role_hod->id,
            'office_id' => $office->id,
        ]);
    }
    public function getesdata()
    {
         dd($this->elasticsearchService->getPaginatedIndexData('users'));
    }
}
