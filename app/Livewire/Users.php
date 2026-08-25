<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\UserPersonal;
use App\Models\UserRoleSchemeOfficeMapping;
use App\Models\Scheme;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Attributes\Loggable;

class Users extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $search = '';
    protected $listeners = ['userFilter' => 'userFilter'];
    public $role, $selectedMappingLevel, $selectedState, $selectedDistrict, $office;
    public $modalOffice = '';
    public $scheme = '';
    public $modalScheme = '';
    public $schemes = [];
    public $assignments = [];
    public $showEditModal = false;
    public $showPermissionModal = false;
    public $selectedUserId;
    public $fullName = '';
    public $email = '';
    public $mobileNumber = '';
    public $designation = '';
    public $availableOffices = [];
    public $officeAddress = '';
    public function updatedRole($roleId)
{
    if (!$roleId) {
        $this->availableOffices = [];
        return;
    }
    

    $this->availableOffices = DB::table('user_role_scheme_office_mappings as m')
        ->join('office_masters as o', 'o.id', '=', 'm.office_id')
        ->where('m.role_id', $roleId)
        ->where('o.is_active', 1)
        ->select('o.id', 'o.name')
        ->distinct()
        ->orderBy('o.name')
        ->get()
        ->toArray();

    $this->office = '';
}
// public function updatedOffice($officeId)
// {
//     if (!$officeId) {
//         $this->officeAddress = '';
//         return;
//     }

//     $this->officeAddress = DB::table('office_masters')
//         ->where('id', $officeId)
//         ->value('address');
// }
//     public function updatedModalOffice($officeId)
// {
//     $this->loadOfficeAddress($officeId);
// }

// public function loadOfficeAddress($officeId)
// {
//     if (!$officeId) {
//         $this->officeAddress = '';
//         return;
//     }

//     $this->officeAddress = DB::table('office_masters')
//         ->where('id', $officeId)
//         ->value('name');
// }
public function setOfficeAddress($officeName)
{
    $this->officeAddress = $officeName;
}
    
    public function openPermissionModal($userId)
{
    $this->selectedUserId = $userId;

    $this->openEditModal();
}

public function mount()
{
    $this->schemes = Scheme::pluck('name', 'id')->toArray();
}
public function addAssignment()
{
    $this->assignments[] = [
        'id' => null,
        'scheme' => '',
        'role' => '',
        'office' => '',
        'address' => '',
    ];
}
public function removeAssignment($index)
{
    unset($this->assignments[$index]);

    $this->assignments = array_values($this->assignments);
}
    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchEnabled()
            ->setSearchLive()
            ->setBulkActionsEnabled();

        $this->setHideBulkActionsWhenEmptyEnabled();

        $this->setTableWrapperAttributes([
            'class' => 'overflow-x-auto overflow-y-auto max-h-[500px] border rounded-lg shadow-sm',
        ]);

        $this->setTableAttributes([
            'class' => 'min-w-full text-sm text-gray-700 text-center overflow-x-auto',
        ]);

        $this->setTheadAttributes([
            'class' => 'bg-violet-800 text-xs uppercase py-3 px-4 text-white',
        ]);
        $this->setThAttributes(function ($column) {
            return [
                'class' => 'px-4 py-3 text-white bg-violet-800 text-xs',
            ];
        });

        $this->setTdAttributes(function ($row) {
            return [
                'class' => 'px-4 py-3 text-gray-700 text-center',
            ];
        });

        $this->setTbodyAttributes([
            'class' => 'px-4 py-3 divide-y divide-gray-200 bg-white overflow-y-auto',
        ]);
    }
    public function userFilter($filters)
    {
        $this->scheme = $filters['scheme'] ?? null;
        $this->role = $filters['role'] ?? null;
        $this->selectedMappingLevel = $filters['mapping_level'] ?? null;
        $this->selectedState = $filters['state'] ?? null;
        $this->selectedDistrict = $filters['district'] ?? null;
        $this->office = $filters['office'] ?? null;
    }
    
//     public function openEditModal()
// {
//     $this->showEditModal = true;
// }
public function openEditModal()
{
    $mappings = UserRoleSchemeOfficeMapping::where(
        'user_id',
        $this->selectedUserId
    )->get();

    $firstMapping = $mappings->first();

    $this->scheme = $firstMapping?->scheme_id ?? '';
    $this->role = $firstMapping?->role_id ?? '';
    // $this->office = $firstMapping?->office_id ?? '';
    $this->modalOffice = $firstMapping?->office_id ?? '';
    $this->officeAddress = $firstMapping?->office?->name ?? '';


    $this->assignments = $mappings
        ->skip(1)
        ->map(function ($mapping) {
            return [
                'id' => $mapping->id,
                'scheme' => $mapping->scheme_id,
                'role' => $mapping->role_id,
                'office' => $mapping->office_id,
                'address' => '',
            ];
        })
        ->values()
        ->toArray();

    $user = User::find($this->selectedUserId);

    if ($user) {
        $this->fullName = $user->name;
        $this->email = $user->email;
        $this->mobileNumber = $user->mobile_no;
        $this->designation = $user->designation;
    }

    $this->showEditModal = true;
}

// public function saveSchemes()
// {
//     $selectedSchemes = $this->scheme ?? [];

//     $mappings = UserRoleSchemeOfficeMapping::where(
//         'user_id',
//         $this->selectedUserId
//     )->get();

//     if ($mappings->isEmpty()) {
//         return;
//     }

//     $roleId = $mappings->first()->role_id;
//     $officeId = $mappings->first()->office_id;

//     // Remove only unselected schemes
//     if (!empty($selectedSchemes)) {
//         UserRoleSchemeOfficeMapping::where('user_id', $this->selectedUserId)
//             ->whereNotIn('scheme_id', $selectedSchemes)
//             ->delete();
//     }

//     // Add selected schemes
//     foreach ($selectedSchemes as $schemeId) {
//         UserRoleSchemeOfficeMapping::updateOrCreate(
//             [
//                 'user_id' => $this->selectedUserId,
//                 'scheme_id' => $schemeId,
//             ],
//             [
//                 'office_id' => $officeId,
//                 'role_id' => $roleId,
//                 'is_active' => 1,
//             ]
//         );
//     }
//     // Update email
//     $user = User::find($this->selectedUserId);

//     if ($user) {
//         $user->email = $this->email;
//         $user->mobile_no = $this->mobileNumber;
//         $user->save();
//     }

//     $this->showEditModal = false;
// }
public function saveSchemes()
{
    $this->validate([
        'fullName' => 'required',
        'email' => 'required|email',
        'mobileNumber' => 'required',
        'designation' => 'required',
        'modalScheme' => 'required|integer',
    'role' => 'required|integer',
    'modalOffice' => 'required',
    'assignments.*.scheme' => 'required|integer',
    'assignments.*.role' => 'required|integer',
    'assignments.*.office' => 'required',
    ]);
    $mappings = UserRoleSchemeOfficeMapping::where(
        'user_id',
        $this->selectedUserId
    )->get();

    if ($mappings->isEmpty()) {
        return;
    }

    // First container
    $firstMapping = $mappings->first();

   $firstMapping->scheme_id = $this->modalScheme;
    $firstMapping->role_id = $this->role;
    $firstMapping->office_id = $this->modalOffice;
    $firstMapping->save();

    // IDs of additional assignments that still exist
    $keepIds = [];

    foreach ($this->assignments as $assignment) {

    if (
        empty($assignment['scheme']) ||
        empty($assignment['role']) ||
        empty($assignment['office'])
    ) {
        continue;
    }

        // Existing container
        if (!empty($assignment['id'])) {

            $mapping = UserRoleSchemeOfficeMapping::where(
                'id',
                $assignment['id']
            )
            ->where(
                'user_id',
                $this->selectedUserId
            )
            ->first();

            if ($mapping) {
                $mapping->scheme_id = $assignment['scheme'];
                $mapping->role_id = $assignment['role'] ?: $firstMapping->role_id;
                $mapping->office_id = $assignment['office'] ?: $firstMapping->office_id;
                $mapping->is_active = 1;
                $mapping->save();

                $keepIds[] = $mapping->id;
            }
        }

        // New container
        else {
    $mapping = UserRoleSchemeOfficeMapping::create([
        'user_id' => $this->selectedUserId,
        'scheme_id' => $assignment['scheme'],
        'role_id' => $assignment['role'],
        'office_id' => $assignment['office'],
        'is_active' => 1,
    ]);

    $keepIds[] = $mapping->id;
}
    }

    // Delete additional assignments whose containers were removed
    UserRoleSchemeOfficeMapping::where(
        'user_id',
        $this->selectedUserId
    )
    ->where('id', '!=', $firstMapping->id)
    ->when(
        !empty($keepIds),
        function ($query) use ($keepIds) {
            $query->whereNotIn('id', $keepIds);
        }
    )
    ->when(
        empty($keepIds),
        function ($query) {
            $query->where('id', '!=', 0);
        }
    )
    ->delete();

    // Update email/mobile
    $user = User::find($this->selectedUserId);

    if ($user) {
        $user->name = $this->fullName;
        $user->email = $this->email;
        $user->mobile_no = $this->mobileNumber;
        $user->save();
    }

    // $this->showEditModal = false;
    session()->flash('success', 'User role and scheme assignments saved successfully.');
    $this->showEditModal = false;
}

    public function updatedSearch($value): void
    {
        $this->setSearch($value);
        $this->resetPage();
    }
    public function updatedPerPage($value): void
    {
        $this->perPage = (int) $value;
        $this->setPerPage((int) $value);
        $this->resetPage();
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->searchable(),
            Column::make("Name", "name")
                ->searchable(),
            Column::make("Mobile No", "mobile_no")
                ->searchable(),
            Column::make("Email", "email")
                ->searchable(),
            // Column::make("Scheme Name", "id")
            //     ->format(function ($value, $row, Column $column) {
            //         $schemes = $row->RoleSchemeOfficeMappings->pluck('scheme.name')->unique()->implode(', ');
            //         return $schemes ?: 'N/A';
            //     }),
            Column::make("Scheme Name", "id")
    ->format(function ($value, $row, Column $column) {
        $schemes = $row->RoleSchemeOfficeMappings
            ->pluck('scheme.name')
            ->unique()
            ->implode(', ');

        return '<div style="white-space: normal; word-break: break-word; max-width: 300px;">'
            . e($schemes ?: 'N/A')
            . '</div>';
    })
    ->html(),

            Column::make('Actions') 
    ->label(fn($row) => 
      view('coulmn_button.assignpermissionrolebutton', [
    'itemId' => $row->id,
    'showPermissionModal' => $this->showPermissionModal,
    'showEditModal' => $this->showEditModal,
    'selectedUserId' => $this->selectedUserId,
    'schemes' => $this->schemes,
    'assignments' => $this->assignments,
])->render()
        . view('coulmn_button.ConfirmDeleteButton', [ 
            'itemId' => $row->id, 
            'action' => 'delete', 
            'title' => 'Inactive User', 
            'message' => "This is $row->name, are you want to make this user inactive?", 
            'tooltip' => 'Inactive User', 
        ])->render()
    )
    ->html(),
        ];
    }

    public function builder(): Builder
    {
        $query = User::query()
            ->whereHas('RoleSchemeOfficeMappings.office', function ($q) {
                $q->where('is_active', 1);

                if (!empty($this->role)) {
                    $q->where('role_id', $this->role);
                }

                if (!empty($this->office)) {
                    $q->where('office_id', $this->office);
                }
                if (!empty($this->selectedDistrict)) {
                    $q->where('district_id', $this->selectedDistrict);
                }
                if (!empty($this->selectedMappingLevel)) {
                    $q->where('office_type_id', $this->selectedMappingLevel);
                }
                if (!empty($this->scheme)) {
                    $q->where('scheme_id', $this->scheme);
                }
            })
            ->with(['mappedRoles', 'mappedPermissions', 'RoleSchemeOfficeMappings.scheme'])
            ->orderBy('id', 'asc');
        return $query;
    }
    #[Loggable(level: 'C', nickname: 'Delete User')]
    public function delete($id)
    {
        $user = User::find($id);

        if ($user) {
            DB::beginTransaction();
            try {
                // UserRoleSchemeOfficeMapping::where('user_id', $id)->get()->each->delete();
                // UserPersonal::where('user_id', $id)->get()->each->delete();
                // $user->delete();
                $user->is_active = 0;
                $user->save();

                DB::commit();

                $this->dispatch('toastr', [
                    'type' => 'success',
                    // 'message' => 'User deleted successfully!',
                    'message' => 'User marked as inactive successfully!',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => 'Failed to delete user: ' . $e->getMessage()
                ]);
            }
        }
    }
}
