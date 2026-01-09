<?php

namespace App\Livewire\Users;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\State;
use App\Models\Scheme;
use Livewire\Component;
use App\Models\District;
use App\Models\Codemaster;
use App\Models\ModelHasPermission;
use App\Models\OfficeMaster;
use App\Models\Permission;
use App\Models\UserPersonal;
use Illuminate\Support\Facades\Hash;
use App\Models\RoleOfficeTypeMapping;
use Illuminate\Support\Facades\Config;
use App\Models\UserRoleSchemeOfficeMapping;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $name, $email, $password, $mobile;
    public $role, $mapping_level, $selectscheme, $office, $selectedMappingLevel, $selectedState, $scheme, $selectedDistrict, $Role, $role_id;

    public $roles = [], $schemes = [], $offices = [], $states = [], $mapping_levels = [], $districts = [];

    protected $rules = [
        'name'     => 'required|string|max:255',
        'mobile' => 'required|digits:10|unique:users,mobile_no',
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
        // $this->states = State::orderBy('name', 'asc')->get();
        $this->states = State::where('is_active', 1)->where('lgd_code',  19)->get();
        $this->districts = District::orderBy('name', 'asc')->get();
    }
    public function updatedRole($value)
    {
        $this->mapping_level = null;
        $this->mapping_levels = [];
        $this->office = null;
        $this->offices = [];
        $this->selectedMappingLevel = null;

        if ($value) {
            $this->mapping_levels = RoleOfficeTypeMapping::with('officeType')
                ->where('role_id', $value)
                ->whereHas('officeType', function ($q) {
                    $q->whereIn('code', [151, 152, 153, 154]);
                })
                ->get()
                ->unique('office_type_id');
        }

        if (!in_array($value, [153, 154])) {
            $this->selectedDistrict = null;
        }
    }

    public function updatedSelectedMappingLevel($value)
    {
        $this->office = null;
        $this->offices = [];

        if ($value) {
            $this->offices = OfficeMaster::where('office_type_id', $value)->get();
        }

        if (!in_array($value, [153, 154])) {
            $this->selectedDistrict = null;
        }
    }

    public function updatedSelectedDistrict($districtId)
    {
        $this->office = null;
        $this->offices = [];

        if ($districtId && in_array($this->selectedMappingLevel, [153, 154])) {
            $this->offices = OfficeMaster::where('office_type_id', $this->selectedMappingLevel)
                ->where('district_id', $districtId)
                ->get();
        }
    }

    public function submit()
    {
        $this->validate();

        // $c_time = Carbon::now()->format('Y-m-d H:i:s');
        // $password_expires_at = Carbon::now()
        //     ->addDays(intval(Config::get('app.password_expire_day')))
        //     ->format('Y/m/d H:i:s');
        $c_time = Carbon::now()->format('Y/m/d H:i:s');

        $password_expires_at = Carbon::now()
            ->addDays((int) Config::get('app.password_expire_day'))
            ->format('Y/m/d H:i:s');

        try {
            DB::beginTransaction();

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
                'name' => $user->name,
            ]);

            $role = Role::find($this->role);

            if ($role) {
                $user->assignRole($role);
                $permissions = $role->permissions;
                // dd($permissions);
                if ($permissions->isNotEmpty()) {
                    $user->givePermissionTo($permissions);
                }
            }

            UserRoleSchemeOfficeMapping::create([
                'user_id' => $user->id,
                'scheme_id' => $this->selectscheme,
                'role_id' => $this->role,
                'office_id' => $this->office,
            ]);

            DB::commit();

            session()->flash('success', 'User created successfully!');
            return redirect()->route('user-managements.index');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }
    public function render()
    {
        return view('livewire.users.create');
    }
}
