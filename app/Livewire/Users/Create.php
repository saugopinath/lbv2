<?php

namespace App\Livewire\Users;

use App\Models\Role;
use App\Models\User;
use App\Models\State;
use App\Models\Scheme;
use Livewire\Component;
use App\Models\Codemaster;
use App\Models\OfficeMaster;
use App\Models\UserPersonal;
use App\Models\UserRoleSchemeOfficeMapping;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class Create extends Component
{
    public $name, $email, $password, $mobile;
    public $role, $selectscheme, $office, $selectedMappingLevel, $selectedState, $scheme;

    public $roles = [], $schemes = [], $offices = [], $states = [], $mapping_levels = [];

    protected $rules = [
        'name'     => 'required|string|max:255',
        'mobile'   => 'required|digits:10',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role'     => 'required|exists:roles,id',
        'selectscheme' => 'required|exists:schemes,id',
        'office'   => 'required|exists:office_masters,id',
    ];

    public function mount()
    {
        $this->roles = Role::all();
        $this->schemes = Scheme::all();
        // $this->offices = OfficeMaster::all();
        $this->states = State::orderBy('name', 'asc')->get();
        $officetype = Codemaster::getIdByCode(15);
        $this->mapping_levels = Codemaster::where('parent_id', $officetype)->whereIn('code', [151, 152, 153, 154])->get();
    }

    public function updatedselectedMappingLevel($value)
    {
         $this->offices = OfficeMaster::where('office_type_id',$value)->get();
    }
    public function submit()
    {
        $this->validate();

        $c_time = Carbon::now()->format('Y-m-d H:i:s');

        $password_expires_at = Carbon::now()->addDays(intval(Config::get('app.password_expire_day')))->format('Y/m/d H:i:s');

        $user = User::create([
            'name' => $this->name,
            'mobile_no' => $this->mobile,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'password_set_time' => $c_time,
            'password_expires_at' => $password_expires_at,
        ]);

        UserPersonal::create([
            'user_id' => $user->id,
            'name'    => $user->name,
        ]);

        $role = Role::find($this->role);

        if ($role) {
            $user->assignRole($role);
        }

        UserRoleSchemeOfficeMapping::create([
            'user_id'  => $user->id,
            'scheme_id' => $this->selectscheme,
            'role_id'  => $this->role,
            'office_id' => $this->office,
        ]);

        session()->flash('success', 'User created successfully!');

        return redirect()->route('user-managements.index');
    }

    public function render()
    {
        return view('livewire.users.create');
    }
}
