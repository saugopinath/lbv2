<?php

namespace App\Livewire;

use App\Models\Role;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class RolerankManagement extends Component
{
    public $schemeId;
    public $roles = [];

    public function mount($schemeId)
    {
        $this->schemeId = $schemeId;
        $this->loadRoles();
    }

    public function loadRoles()
    {
        $this->roles = Role::orderBy('rank', 'asc')
            ->get()
            ->mapWithKeys(function ($role) {
                return [
                    $role->id => [
                        'id' => $role->id,
                        'name' => $role->name,
                        'rank' => $role->rank,
                        'same_as_prev' => false,
                    ]
                ];
            })
            ->toArray();
    }

    public function updateOrder($orderedIds)
    {
        $newOrder = [];

        foreach ($orderedIds as $position => $id) {
            if (isset($this->roles[$id])) {

                // First item can never be same as previous
                if ($position === 0) {
                    $this->roles[$id]['same_as_prev'] = false;
                }

                $newOrder[$id] = $this->roles[$id];
            }
        }

        $this->roles = $newOrder;
    }

    public function saveMapping()
    {
        DB::transaction(function () {

            $currentRank = 1;
            $isFirst = true;

            foreach ($this->roles as $roleData) {

                if (!$isFirst) {
                    if (!($roleData['same_as_prev'] ?? false)) {
                        $currentRank++;
                    }
                }

                Role::where('id', $roleData['id'])
                    ->update(['rank' => $currentRank]);

                $isFirst = false;
            }
        });

        $this->loadRoles();

        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Hierarchy saved!'
        ]);
    }

    public function render()
    {
        return view('livewire.rolerank-management');
    }
}
