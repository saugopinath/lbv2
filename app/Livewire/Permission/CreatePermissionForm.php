<?php

namespace App\Livewire\Permission;

use App\Models\Permission;
use App\Models\ValidationScoreMapping;
use Livewire\Component;

class CreatePermissionForm extends Component
{

    public $name;
    public $is_parent = '';
    public $parent_id = null;
    public $has_score = null;
    public $min_score;
    public $max_score;



    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'is_parent'  => 'required|in:0,1',


        ];
        if ($this->is_parent == '1') {
            $rules['parent_id'] = 'required|exists:permissions,id';
        }
        if ($this->is_parent == '0') {
            $rules['has_score'] = 'required|in:0,1';
        }
        if ($this->has_score == 1) {
            $rules['min_score'] = 'required|integer|min:0';
            $rules['max_score'] = 'required|integer|gte:min_score';
        }

        return $rules;
    }
    public function massages()
    {
        return [
            'name.required' => 'The permission name is required.',
            'is_parent.required' => 'Please specify if this is a parent permission.',
            'is_parent.in' => 'Invalid value for parent permission selection.',
            'has_score.required' => 'Please specify if this permission has a score range.',
            'has_score.in' => 'Invalid value for score range selection.',
            'min_score.required_if' => 'Minimum score is required when score range is enabled.',
            'min_score.integer' => 'Minimum score must be an integer.',
            'min_score.min' => 'Minimum score must be at least 0.',
            'max_score.required_if' => 'Maximum score is required when score range is enabled.',
            'max_score.integer' => 'Maximum score must be an integer.',
            'max_score.gte' => 'Maximum score must be greater than or equal to minimum score.',
        ];
    }
    public function save()
    {

        $this->validate();

        if ($this->is_parent == '0') {
            $this->parent_id = null;
        }
        // dd([
        //             'name'       => $this->name,
        //             'is_parent'  => $this->is_parent,
        //             'parent_id'  => $this->parent_id,
        //         ]);  
        $permission = Permission::create([
            'name'       => $this->name,
            'guard_name' => 'web',
            'parent_id'  => $this->parent_id,
        ]);
        if ($this->has_score == 1) {
            ValidationScoreMapping::create([
                'permission_id' => $permission->id,
                'min_score'     => $this->min_score,
                'max_score'     => $this->max_score,
            ]);
        }

        $this->reset(['name', 'is_parent', 'parent_id', 'has_score', 'min_score', 'max_score']);
        $this->dispatch('close-modal');
        $this->dispatch('notify', message: 'Permission created successfully!');
        $this->dispatch('refreshDatatable');
    }
    public function cancel()
    {
        $this->reset(['name', 'is_parent', 'parent_id', 'has_score', 'min_score', 'max_score']);
        $this->dispatch('close-modal');
    }

    public function getParentsProperty()
    {
        return Permission::whereNull('parent_id')->get();
    }
    public function render()
    {
        return view('livewire.permission.create-permission-form', [
            'parents' => $this->parents, // explicitly pass to blade
        ]);
    }
}
