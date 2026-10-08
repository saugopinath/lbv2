<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Scheme;
use App\Models\Department;
use App\Models\DynamicWorkflowSchemeModule;
use App\Models\WorkflowStep;
use App\Models\UserRoleSchemeOfficeMapping;
use App\Models\DynamicWorkflowRequest;
use App\Models\DupcheckschemeconfigSetting;
use App\Models\AgeManagements;
use App\Models\SchemeCapacity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Attributes\Loggable;

class SchemesTable extends Component
{
    use WithPagination;

    // Filters & Pagination
    public string $search = '';
    public int $perPage = 10;
    public array $selectedDepartments = [];
    public string $statusFilter = '';
    public array $departmentsList = [];

    // Create Modal State
    public bool $showCreateModal = false;
    public string $createName = '';
    public string $createShortName = '';
    public ?int $createDepartmentId = null;
    public string $createDescription = '';

    // Edit Modal State
    public bool $showEditModal = false;
    public ?int $editingSchemeId = null;
    public string $editName = '';
    public string $editShortName = '';
    public ?int $editDepartmentId = null;
    public string $editDescription = '';
    public int $editIsActive = 1;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->departmentsList = Department::select('id', 'name', 'short_name')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($d) {
                return [$d->id => $d->name . ($d->short_name ? ' (' . $d->short_name . ')' : '')];
            })
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

    public function updatedSelectedDepartments()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'selectedDepartments', 'statusFilter']);
        $this->resetPage();
    }

    // Modal Control Methods
    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['createName', 'createShortName', 'createDepartmentId', 'createDescription']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['createName', 'createShortName', 'createDepartmentId', 'createDescription']);
        $this->resetValidation();
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $scheme = Scheme::select('id', 'name', 'short_name', 'department_id', 'description', 'is_active')->find($id);
        if (!$scheme) {
            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => 'Scheme not found.'
            ]);
            return;
        }

        $this->editingSchemeId = $scheme->id;
        $this->editName = $scheme->name;
        $this->editShortName = $scheme->short_name;
        $this->editDepartmentId = $scheme->department_id;
        $this->editDescription = $scheme->description ?? '';
        $this->editIsActive = (int) $scheme->is_active;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editingSchemeId', 'editName', 'editShortName', 'editDepartmentId', 'editDescription', 'editIsActive']);
        $this->resetValidation();
    }

    // CRUD Action Methods
    #[Loggable(level: 'C', nickname: 'Scheme Create')]
    public function saveScheme()
    {
        $this->validate([
            'createName'         => 'required|min:3|max:255|string|unique:schemes,name',
            'createShortName'    => 'required|min:2|max:20|string|unique:schemes,short_name',
            'createDepartmentId' => 'required|exists:departments,id',
            'createDescription'  => 'nullable|string|max:255',
        ], [
            'createName.required'         => 'Scheme Name is required.',
            'createName.unique'           => 'A Scheme with this name already exists.',
            'createShortName.required'    => 'Scheme Short-Name is required.',
            'createShortName.unique'      => 'A Scheme with this short-name already exists.',
            'createDepartmentId.required' => 'Please select a Department.',
        ]);

        Scheme::create([
            'name'          => strtoupper(trim($this->createName)),
            'short_name'    => trim($this->createShortName),
            'department_id' => $this->createDepartmentId,
            'description'   => !empty($this->createDescription) ? trim($this->createDescription) : null,
            'is_active'     => 1,
        ]);

        $this->closeCreateModal();
        $this->dispatch('toastr', [
            'type'    => 'success',
            'message' => 'Scheme created successfully!'
        ]);
    }

    #[Loggable(level: 'U', nickname: 'Scheme Update')]
    public function updateScheme()
    {
        if (!$this->editingSchemeId) {
            return;
        }

        $this->validate([
            'editName'         => 'required|min:3|max:255|string|unique:schemes,name,' . $this->editingSchemeId,
            'editShortName'    => 'required|min:2|max:20|string|unique:schemes,short_name,' . $this->editingSchemeId,
            'editDepartmentId' => 'required|exists:departments,id',
            'editDescription'  => 'nullable|string|max:255',
            'editIsActive'     => 'required|in:0,1',
        ], [
            'editName.required'         => 'Scheme Name is required.',
            'editName.unique'           => 'A Scheme with this name already exists.',
            'editShortName.required'    => 'Scheme Short-Name is required.',
            'editShortName.unique'      => 'A Scheme with this short-name already exists.',
            'editDepartmentId.required' => 'Please select a Department.',
        ]);

        $scheme = Scheme::select('id', 'name', 'short_name', 'department_id', 'description', 'is_active')->find($this->editingSchemeId);
        if ($scheme) {
            $scheme->update([
                'name'          => strtoupper(trim($this->editName)),
                'short_name'    => trim($this->editShortName),
                'department_id' => $this->editDepartmentId,
                'description'   => !empty($this->editDescription) ? trim($this->editDescription) : null,
                'is_active'     => (int) $this->editIsActive,
            ]);

            $this->closeEditModal();
            $this->dispatch('toastr', [
                'type'    => 'success',
                'message' => "Scheme '{$scheme->name}' updated successfully!"
            ]);
        }
    }

    #[Loggable(level: 'U', nickname: 'Scheme Toggle Status')]
    public function toggleStatus($id)
    {
        $scheme = Scheme::select('id', 'is_active', 'name')->find($id);
        if ($scheme) {
            $scheme->is_active = $scheme->is_active ? 0 : 1;
            $scheme->save();

            $statusMessage = $scheme->is_active
                ? "Scheme '{$scheme->name}' enabled successfully!"
                : "Scheme '{$scheme->name}' disabled successfully!";

            $this->dispatch('toastr', [
                'type'    => 'success',
                'message' => $statusMessage
            ]);
        } else {
            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => 'Scheme not found.'
            ]);
        }
    }

    #[Loggable(level: 'D', nickname: 'Scheme Protected Delete')]
    public function delete($id)
    {
        $scheme = Scheme::select('id', 'name')->find($id);
        if (!$scheme) {
            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => 'Scheme not found.'
            ]);
            return;
        }

        // PROTECTED DELETION GUARD: Fast indexed exists checks across dependent domain models
        $hasWorkflows    = DynamicWorkflowSchemeModule::where('scheme_id', $id)->exists();
        $hasSteps        = WorkflowStep::where('scheme_id', $id)->exists();
        $hasUserMappings = UserRoleSchemeOfficeMapping::where('scheme_id', $id)->exists();
        $hasRequests     = DynamicWorkflowRequest::where('scheme_id', $id)->exists();
        $hasDupSettings  = DupcheckschemeconfigSetting::where('scheme_id', $id)->exists();
        $hasAgeSettings  = AgeManagements::where('scheme_id', $id)->exists();
        $hasCapacities   = SchemeCapacity::where('scheme_id', $id)->exists();

        $hasFormDocs = false;
        try {
            if (Schema::hasTable('scheme_attached_doc_mappings')) {
                $hasFormDocs = DB::table('scheme_attached_doc_mappings')->where('scheme_id', $id)->exists();
            }
        } catch (\Exception $e) {
            $hasFormDocs = false;
        }

        $hasFormTabs = false;
        try {
            if (Schema::hasTable('scheme_tab_basefields')) {
                $hasFormTabs = DB::table('scheme_tab_basefields')->where('scheme_id', $id)->exists();
            }
        } catch (\Exception $e) {
            $hasFormTabs = false;
        }

        $hasBeneficiaries = false;
        try {
            if (Schema::hasTable('pension.beneficiary_personals')) {
                $hasBeneficiaries = DB::table('pension.beneficiary_personals')->where('scheme_id', $id)->exists();
            }
        } catch (\Exception $e) {
            $hasBeneficiaries = false;
        }

        if ($hasWorkflows || $hasSteps || $hasUserMappings || $hasRequests || $hasDupSettings || $hasAgeSettings || $hasCapacities || $hasFormDocs || $hasFormTabs || $hasBeneficiaries) {
            $reasons = [];
            if ($hasWorkflows)    $reasons[] = 'Configured Workflows';
            if ($hasSteps)        $reasons[] = 'Workflow Steps';
            if ($hasUserMappings) $reasons[] = 'User Office Mappings';
            if ($hasRequests || $hasBeneficiaries) $reasons[] = 'Applications/Requests';
            if ($hasFormDocs || $hasFormTabs) $reasons[] = 'Form Configuration / Documents';
            if ($hasDupSettings || $hasAgeSettings || $hasCapacities) $reasons[] = 'Scheme Settings';

            $reasonText = implode(', ', $reasons);

            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => "Cannot delete scheme '{$scheme->name}'. It is protected because active records exist in: {$reasonText}."
            ]);
            return;
        }

        try {
            DB::transaction(function () use ($scheme) {
                $scheme->delete();
            });

            $this->dispatch('toastr', [
                'type'    => 'success',
                'message' => "Scheme '{$scheme->name}' deleted successfully!"
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => "Cannot delete scheme '{$scheme->name}': Foreign key references exist."
            ]);
        }
    }

    public function render()
    {
        $query = Scheme::with(['Department:id,name,short_name'])
            ->select('schemes.id', 'schemes.name', 'schemes.short_name', 'schemes.department_id', 'schemes.description', 'schemes.is_active');

        if (!empty(trim($this->search))) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('schemes.name', 'ILIKE', $searchTerm)
                    ->orWhere('schemes.short_name', 'ILIKE', $searchTerm)
                    ->orWhere('schemes.description', 'ILIKE', $searchTerm)
                    ->orWhereRaw("CAST(schemes.id AS TEXT) ILIKE ?", [$searchTerm])
                    ->orWhereHas('Department', function ($deptQ) use ($searchTerm) {
                        $deptQ->where('name', 'ILIKE', $searchTerm)
                              ->orWhere('short_name', 'ILIKE', $searchTerm);
                    });
            });
        }

        if (!empty($this->selectedDepartments)) {
            $query->whereIn('schemes.department_id', (array) $this->selectedDepartments);
        }

        if ($this->statusFilter === 'active') {
            $query->where('schemes.is_active', 1);
        } elseif ($this->statusFilter === 'disabled') {
            $query->where('schemes.is_active', 0);
        }

        $schemes = $query->orderBy('schemes.id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.schemes-table', [
            'schemes' => $schemes
        ]);
    }
}
