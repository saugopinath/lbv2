<?php

namespace App\Livewire;

use App\Helpers\SchemeCapacityHelper;
use App\Models\Scheme;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class SchemeDropdown extends Component
{
    public $schemes;

    public $schemeName;

    public $schemeId = null;

    public $option = null;

    public $button_show;

    public $currentRoute;

    public function mount($schemes, $scheme_id = null)
    {
        $this->schemes = $schemes;

        $this->button_show = 1;

        $this->currentRoute = Route::currentRouteName();

        if ($this->currentRoute === 'schemes.final-submitted') {
            $this->option = 1;
        } elseif ($this->currentRoute === 'define-workflow') {
            $this->option = 2;
        } elseif ($this->currentRoute === 'lb-application-list') {
            $this->option = 3;
        }

        if ($scheme_id) {
            try {

                $this->schemeId = Crypt::decryptString($scheme_id);

                $this->schemeName = Scheme::where('id', $this->schemeId)->value('name');
            } catch (\Exception $e) {

                $this->schemeId = null;

                $this->schemeName = null;
            }
        }
    }

    public function updatedSchemeId($value)
    {
        $scheme = Scheme::with([
            'capacities' => fn($q) => $q->active()
        ])->find($value);
        // dd($scheme->capacities->first()->total_capacity);
        $this->schemeName = $scheme?->name;

        if ($this->currentRoute !== 'schemes.final-submitted') {
            return;
        }

        $filter = $this->getFilterData();

        $result = SchemeCapacityHelper::check(
            $value,
            0,
            $filter
        );
        if (is_array($result)) {

            $msg = "{$result['model']} capacity full. 
            Total: {$result['total']} 
            Processed: {$result['processed']} 
            Remaining: {$result['remaining']}";

            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => $msg,
            ]);

            $this->schemeId = null;
            $this->schemeName = null;
        }
    }

    private function getFilterData()
    {
        $filter = [];

        $select_lgd = session('lgd_session');

        if (! empty($select_lgd['district_id'])) {

            $filter['created_by_dist_code'] =
                Crypt::decryptString($select_lgd['district_id']);
        }

        if (! empty($select_lgd['block_id'])) {

            $filter['created_by_local_body_code'] =
                Crypt::decryptString($select_lgd['block_id']);
        }

        if (! empty($select_lgd['subdivision_id'])) {

            $filter['created_by_subdivision_code'] =
                Crypt::decryptString($select_lgd['subdivision_id']);
        }

        return $filter;
    }

    public function render()
    {
        return view('livewire.scheme-dropdown');
    }
}
