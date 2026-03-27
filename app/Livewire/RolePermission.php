<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class RolePermission extends Component
{
    public bool $schemeData = false;
    public $schemeId, $schemeName = null;
    public ?string $stage = null;

    public function mount($stage = null)
    {
        $this->stage = $stage;
    }

    #[On('selectedScheme')]
    public function updateschemeData($schemeData)
    {
        if ($schemeData) {
            $this->schemeData = true;
            $this->schemeId = $schemeData['scheme_id'];
            $this->schemeName = $schemeData['scheme_name'];

            // Sync with session for consistent helper behavior
            session(['scheme_id' => $this->schemeId]);

            // Also update the Spatie Registrar for the current request
            app(\Spatie\Permission\PermissionRegistrar::class)
                ->setPermissionsTeamId($this->schemeId);
        } else {
            $this->schemeData = false;
        }
    }

    public function render()
    {
        return view('livewire.role-permission');
    }
}
