<?php
namespace App\Livewire;
use App\Models\WorkflowStep;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

class CreateworkflowSteps extends Component
{
    public $schemeId;
    public $noofSteps;
    public $labels = [];
    public bool $already = false;
    public function mount($schemeId)
    {
        $this->schemeId = $schemeId;
        $steps = WorkflowStep::where('scheme_id', $schemeId)
                    ->orderBy('rank')
                    ->get();
        if ($steps->isNotEmpty()) {
            $this->noofSteps = $steps->count();
            $this->labels = $steps->pluck('label')->toArray();
            $this->already = true;
        }
    }
    protected function rules()
    {
        return [
            'noofSteps' => 'required|integer|min:1',
            'labels.*'  => 'required',
        ];
    }
    protected function messages()
    {
        return [
            'noofSteps.*' => 'Number of steps is required.',
            'labels.*.*'  => 'Label Name is required.',
        ];
    }
    public function updatedNoofSteps($value)
    {
        $value = (int) $value;
        if ($value < 1) {
            $this->labels = [];
            return;
        }
        $existing = $this->labels;
        $this->labels = [];
        for ($i = 0; $i < $value; $i++) {
            $this->labels[$i] = $existing[$i] ?? '';
        }
    }
    public function save()
    {
        $this->validate();
        DB::transaction(function () {
            $parentId = null;
            $totalSteps = count($this->labels);
            for ($i = 0; $i < $totalSteps; $i++) {
                $step = new WorkflowStep();
                $step->scheme_id = $this->schemeId;
                $step->rank      = $i + 1;
                $step->label     = $this->labels[$i];
                $step->parent_id = $parentId;
                $step->is_first  = ($i === 0);
                $step->is_last   = ($i === $totalSteps - 1);
                $step->save();
                $parentId = $step->id;
            }
        });
    }
    public function render()
    {
        return view('livewire.createworkflow-steps');
    }
}