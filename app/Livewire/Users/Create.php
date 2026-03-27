<?php

namespace App\Livewire\Users;

use App\Attributes\Loggable;
use App\Models\District;
use App\Models\OfficeMaster;
use App\Models\Role;
use App\Models\RoleOfficeTypeMapping;
use App\Models\Scheme;
use App\Models\State;
use App\Models\User;
use App\Models\UserPersonal;
use App\Models\UserRoleSchemeOfficeMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

class Create extends Component
{
    public $name;

    public $email;

    public $password;

    public $mobile;

    public $role;

    public $mapping_level;

    public $selectscheme;

    public $office;

    public $selectedMappingLevel;

    public $selectedState;

    public $selectedDistrict;

    public $Role;

    public $role_id;

    public $roles = [];

    public $offices = [];

    public $states = [];

    public $mapping_levels = [];

    public $districts = [];

    public $scheme = [];

    public $schemes = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'mobile' => 'required|digits:10|unique:users,mobile_no',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role' => 'required|exists:roles,id',
        'scheme' => 'required|array|min:1',
        'scheme.*' => 'exists:schemes,id',
        'selectedMappingLevel' => 'required|exists:codemasters,code',
        'selectedState' => 'required|exists:states,id',
        'office' => 'required|exists:office_masters,id',
    ];

    public function mount()
    {
        $this->roles = Role::all();
        $this->schemes = Scheme::pluck('name', 'id')->toArray();
        $this->states = State::where('is_active', 1)
            ->where('lgd_code', 19)
            ->get();
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

        if (! in_array($value, [153, 154])) {
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

        if (! in_array($value, [153, 154])) {
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

    #[Loggable(level: 'C', nickname: 'Create User')]
    public function submit()
    {
        $rules = $this->rules;

        // Conditionally require district
        if (in_array($this->selectedMappingLevel, [153, 154])) {
            $rules['selectedDistrict'] = 'required|exists:districts,id';
        }

        $this->validate($rules);

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

            foreach ($this->scheme as $schemeId) {

                // 🚀 MOST IMPORTANT
                app(PermissionRegistrar::class)
                    ->setPermissionsTeamId($schemeId);

                if ($role) {

                    // assign role per scheme
                    $user->assignRole($role);

                    // assign permissions per scheme
                    $permissions = $role->permissions;

                    if ($permissions->isNotEmpty()) {

                        $user->givePermissionTo(
                            $permissions
                        );
                    }
                }

                // Save mapping
                UserRoleSchemeOfficeMapping::create([
                    'user_id' => $user->id,
                    'scheme_id' => $schemeId,
                    'role_id' => $this->role,
                    'office_id' => $this->office,
                ]);
            }

            DB::commit();

            session()->flash('success', 'User created successfully!');

            return redirect()->route('user-managements');
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong: '.$e->getMessage())->withInput();
        }
    }

    public function updateReset()
    {
        $this->reset([
            'name',
            'mobile',
            'email',
            'password',
            'role',
            'mapping_level',
            'office',
            'selectedMappingLevel',
            'selectedState',
            'scheme',
            'selectedDistrict',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.users.create');
    }
}
