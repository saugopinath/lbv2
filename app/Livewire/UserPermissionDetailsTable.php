<?php

namespace App\Livewire;

use App\Models\Codemaster;
use App\Models\District;
use App\Models\OfficeMaster;
use App\Models\Role;
use App\Models\RoleOfficeTypeMapping;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class UserPermissionDetailsTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public $role, $selectedMappingLevel, $selectedState, $selectedDistrict, $office;

    protected $listeners = ['refreshUserTable' => '$refresh',  'userFilter' => 'userFilter'];

    public function userFilter($filters)
    {
        $this->role = $filters['role'] ?? null;
        $this->selectedMappingLevel = $filters['mapping_level'] ?? null;
        $this->selectedState = $filters['state'] ?? null;
        $this->selectedDistrict = $filters['district'] ?? null;
        $this->office = $filters['office'] ?? null;

        // Add any derived logic here if needed
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setPerPageAccepted([5, 10]);
        $this->setPerPage($this->perPage);
        $this->setBulkActionsEnabled();
        $this->setHideBulkActionsWhenEmptyEnabled();

        $this->setTableWrapperAttributes([
            'class' => 'overflow-x-auto overflow-y-auto max-h-[500px] border rounded-lg shadow-sm',
        ]);

        $this->setTableAttributes([
            'class' => 'min-w-full text-sm text-gray-700 text-center overflow-x-auto',
        ]);

        $this->setTheadAttributes([
            'class' => 'bg-violet-800 text-sm uppercase py-3 px-4 text-white',
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
        $this->setLoadingPlaceholderEnabled();
    }

    public function bulkActions(): array
    {
        return [
            'assign_bulk_permissions' => 'Assign Permission',
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
            })
            ->with(['mappedRoles', 'mappedPermissions']);

        return $query;
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")->hideIf(true),

            Column::make("User Name", "name")->searchable(),

            Column::make("Mobile No", "mobile_no")->searchable(),

            Column::make("Role")
                ->label(fn($row) => $row->mappedRoles->map(fn($role) => "<span class='px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs'>{$role->name}</span>")->implode(' '))
                ->html(),
            Column::make("Assigned Permissions")
                ->label(function ($row) {
                    $colors = [
                        'bg-blue-100 text-blue-800 border border-blue-200',
                        'bg-green-100 text-green-800 border border-green-200',
                        'bg-purple-100 text-purple-800 border border-purple-200',
                        'bg-pink-100 text-pink-800 border border-pink-200',
                        'bg-yellow-100 text-yellow-800 border border-yellow-200',
                    ];

                    $permissions = $row->mappedPermissions->values();

                    $tags = $permissions->map(function ($permission, $index) use ($colors) {
                        $color = $colors[$index % count($colors)];
                        return "<span class='px-2 py-1 rounded text-xs font-medium {$color}'>{$permission->name}</span>";
                    });

                    $collapsed = $tags->take(3)->implode(' ');
                    $hidden = $tags->skip(3)->implode(' ');

                    // Wrap in Alpine.js for collapsible toggle
                    return "
            <div x-data=\"{ open: false }\" class='max-w-xs'>
                <div class='flex flex-wrap gap-1'>
                    {$collapsed}
                    <template x-if='open'>
                        <span class='flex flex-wrap gap-1'>{$hidden}</span>
                    </template>
                </div>
                " . ($permissions->count() > 3 ? "
                    <button type='button'
                        class='text-blue-600 text-xs mt-1 focus:outline-none'
                        @click='open = !open'
                        x-text='open ? \"Show less\" : \"Show more\"'></button>" : "") . "</div>";
                })
                ->html(),

            Column::make("Actions")
                ->label(
                    fn($row) =>
                    view('coulmn_button.actions', [
                        'wireClick' => " \$dispatch('UpdatePermission', { userId: {$row->id} })",
                        'tooltip'   => 'Update Permissions',
                    ])->render()
                )
                ->html(),
        ];
    }
    public function assign_bulk_permissions()
    {
        // dd($this->getSelected());
        $this->dispatch('open-bulk-assign-permission-modal',  users: $this->getSelected());
    }
    #[On('assign-success')]
    public function assignsuccessfully()
    {
        $this->clearSelected();
        $this->dispatch('refreshUserTable');
        session()->flash('success', 'Bulk selection cleared successfully.');
    }
}
