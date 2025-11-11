<?php

namespace App\Livewire;

// use App\Models\Permission;
use App\Models\Role;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class RolePermissionManagementDetailsTable extends DataTableComponent
{

    protected $listeners = ['refreshDatatable' => '$refresh'];
    public int $rowNumberOffset = 0;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->rowNumberOffset = ($this->getPage() - 1) * $this->getPerPage();

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
        $this->setLoadingPlaceholderEnabled();
    }
    public function builder(): Builder
    {
        $query = Role::query()
            ->with('mappedPermissions');

        return $query;
    }

    public function columns(): array
    {
        return [
            Column::make("No.")
                ->label(function ($value, $row) {
                    static $i = 0; // counter per page
                    $i++;
                    return ($this->getPage() - 1) * $this->getPerPage() + $i;
                }),
            Column::make("ID", "id")->hideIf(true),
            Column::make("Name", "name")->searchable(),
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
            Column::make("Update Permissions")
                ->label(
                    fn($row) =>
                    view('coulmn_button.actions', [
                        'wireClick' => " \$dispatch('UpdateRolePermission', { roleId: {$row->id} })",
                        'tooltip'   => 'Update Permissions',
                    ])->render()
                )
                ->html(),
            Column::make('Actions')
                ->label(fn($row) => view('coulmn_button.ConfirmDeleteButton', [
                    'itemId' => $row->id,
                    'action' => 'delete',
                    'title' => 'Delete Role',
                    'message' => "This is $row->name , are you want to delete this role?",
                    'tooltip' => 'Delete Role',
                    
                ])->render())
                ->html(),
        ];
    }

    public function delete($id)
    {

        $role = Role::find($id);
        // dd($role);
        if ($role) {
            $role->delete();
            $this->dispatch('toastr', [
                        'type' => 'warning',
                        'message' => 'Role Deleted successfully!']);
            $this->dispatch('refreshDatatable');
        }
    }
}
