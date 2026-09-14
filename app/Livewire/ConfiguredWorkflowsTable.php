<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DynamicWorkflowSchemeModule;
use App\Models\DynamicWorkflowLabel;
use App\Models\workflowstepRolemapping;

class ConfiguredWorkflowsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $schemeModule = DynamicWorkflowSchemeModule::find($id);
        if ($schemeModule) {
            $schemeId = $schemeModule->scheme_id;
            $schemeModuleId = $schemeModule->id;

            // Delete associated steps and role mappings
            workflowstepRolemapping::where('module_id', $schemeModuleId)->where('scheme_id', $schemeId)->delete();
            DynamicWorkflowLabel::where('module_id', $schemeModuleId)->where('scheme_id', $schemeId)->delete();
            $schemeModule->delete();

            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'Configured workflow deleted successfully!'
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
        $query = DynamicWorkflowSchemeModule::with(['scheme', 'module'])
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

        $configuredWorkflows = $query->orderBy('dynamic_workflow_scheme_modules.id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.configured-workflows-table', [
            'configuredWorkflows' => $configuredWorkflows
        ]);
    }
}
