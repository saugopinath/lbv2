<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DynamicWorkflowSchemeModule;
use App\Models\DynamicWorkflowLabel;
use App\Models\workflowstepRolemapping;
use App\Models\DupcheckschemeconfigSetting;
use App\Models\{AgeManagements, Scheme, DynamicWorkflowModule, Role, Permission};
use Illuminate\Support\Facades\DB;

class ConfiguredWorkflowsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public array $selectedSchemes = [];
    public array $selectedModules = [];
    public string $statusFilter = '';
    public array $schemesList = [];
    public array $modulesList = [];

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->schemesList = Scheme::where('is_active', 1)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->modulesList = DynamicWorkflowModule::where('is_active', 1)
            ->orderBy('module_name')
            ->pluck('module_name', 'id')
            ->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatedSelectedSchemes()
    {
        $this->resetPage();
    }

    public function updatedSelectedModules()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'selectedSchemes', 'selectedModules', 'statusFilter']);
        $this->resetPage();
    }

    public function delete($id)
    {
        /* OLD CODE PRESERVED FOR BACKWARD COMPATIBILITY:
        $schemeModule = DynamicWorkflowSchemeModule::select('id', 'scheme_id')->find($id);
        if ($schemeModule) {
            $schemeId = $schemeModule->scheme_id;
            $schemeModuleId = $schemeModule->id;

            workflowstepRolemapping::where('module_id', $schemeModuleId)->where('scheme_id', $schemeId)->delete();
            DynamicWorkflowLabel::where('module_id', $schemeModuleId)->where('scheme_id', $schemeId)->delete();
            DupcheckschemeconfigSetting::where('module_id', $schemeModuleId)->where('scheme_id', $schemeId)->delete();
            AgeManagements::where('module_id', $schemeModuleId)->where('scheme_id', $schemeId)->delete();
            $schemeModule->delete();
        }
        */

        $schemeModule = DynamicWorkflowSchemeModule::with('module')->find($id);
        if ($schemeModule) {
            $schemeId = $schemeModule->scheme_id;
            $schemeModuleId = $schemeModule->id;
            $moduleCode = $schemeModule->module?->module_code ?? $schemeModule->main_module_code ?? '';

            DB::transaction(function () use ($schemeId, $schemeModuleId, $moduleCode, $schemeModule) {
                // 1. Revoke auto-created direct module access permissions for this scheme
                if (!empty($moduleCode)) {
                    $moduleAccessPerm = strtolower(str_replace(' ', '_', $moduleCode)) . '_access';
                    $perm = Permission::where('name', $moduleAccessPerm)->first();
                    if ($perm) {
                        DB::table('model_has_permissions')
                            ->where('permission_id', $perm->id)
                            ->where('team_id', $schemeId)
                            ->delete();
                    }
                }

                // 2. Identify and clean up custom roles generated for this scheme workflow (*_{MODULE_CODE}_{SCHEME_ID})
                if (!empty($moduleCode)) {
                    $customRolePattern = '%_' . strtoupper($moduleCode) . '_' . $schemeId;
                    $customRoles = Role::where('name', 'LIKE', $customRolePattern)->get();

                    foreach ($customRoles as $role) {
                        $role->permissions()->detach();
                        $role->users()->detach();
                        $role->delete();
                    }
                }

                // 3. Delete associated steps, role mappings, dup check, and age management configs
                workflowstepRolemapping::where('module_id', $schemeModuleId)->where('scheme_id', $schemeId)->delete();
                DynamicWorkflowLabel::where('module_id', $schemeModuleId)->where('scheme_id', $schemeId)->delete();
                DupcheckschemeconfigSetting::where('module_id', $schemeModuleId)->where('scheme_id', $schemeId)->delete();
                AgeManagements::where('module_id', $schemeModuleId)->where('scheme_id', $schemeId)->delete();
                $schemeModule->delete();
            });

            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'Configured workflow and associated permissions deleted successfully!'
            ]);
        } else {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Workflow configuration not found.'
            ]);
        }
    }

    public function toggleDisable($id)
    {
        $schemeModule = DynamicWorkflowSchemeModule::select('id', 'is_disabled')->find($id);
        if ($schemeModule) {
            $schemeModule->is_disabled = !$schemeModule->is_disabled;
            $schemeModule->save();

            $statusMessage = $schemeModule->is_disabled ? 'Workflow disabled successfully!' : 'Workflow enabled successfully!';

            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => $statusMessage
            ]);
        } else {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Workflow configuration not found.'
            ]);
        }
    }

    public function render()
    {
        $query = DynamicWorkflowSchemeModule::with(['scheme:id,name', 'module:id,module_name,module_code'])
            ->select('dynamic_workflow_scheme_modules.*')
            ->join('schemes', 'dynamic_workflow_scheme_modules.scheme_id', '=', 'schemes.id')
            ->join('dynamic_workflow_modules', 'dynamic_workflow_scheme_modules.module_id', '=', 'dynamic_workflow_modules.id');

        if (!empty(trim($this->search))) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('schemes.name', 'ILIKE', $searchTerm)
                    ->orWhereRaw("CAST(schemes.id AS TEXT) ILIKE ?", [$searchTerm])
                    ->orWhere('dynamic_workflow_modules.module_name', 'ILIKE', $searchTerm)
                    ->orWhere('dynamic_workflow_modules.module_code', 'ILIKE', $searchTerm)
                    ->orWhere('dynamic_workflow_scheme_modules.main_module_code', 'ILIKE', $searchTerm);
            });
        }

        if (!empty($this->selectedSchemes)) {
            $query->whereIn('dynamic_workflow_scheme_modules.scheme_id', (array) $this->selectedSchemes);
        }

        if (!empty($this->selectedModules)) {
            $query->whereIn('dynamic_workflow_scheme_modules.module_id', (array) $this->selectedModules);
        }

        if ($this->statusFilter === 'active') {
            $query->where('dynamic_workflow_scheme_modules.is_disabled', 0);
        } elseif ($this->statusFilter === 'disabled') {
            $query->where('dynamic_workflow_scheme_modules.is_disabled', 1);
        }

        $configuredWorkflows = $query->orderBy('dynamic_workflow_scheme_modules.id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.configured-workflows-table', [
            'configuredWorkflows' => $configuredWorkflows
        ]);
    }
}
