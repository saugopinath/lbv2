<?php

namespace App\Livewire;

use App\Models\Scheme;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use App\Helpers\SchemeCapacityHelper;

class SchemeDropdown extends Component
{
    public $schemes;
    public $schemeName;
    public $schemeId = null;
    public $option = null;
    public $button_show;

    public function mount($schemes, $scheme_id = null)
    {
        $this->schemes = $schemes;
        $this->button_show = 1;

        $route = Route::currentRouteName();
        if ($route == 'schemes.final-submitted') {
            $this->option = 1;
        } elseif ($route == 'define-workflow') {
            $this->option = 2;
        } elseif ($route == 'lb-application-list') {
            $this->option = 3;
        }

        if ($scheme_id) {
            try {
                $this->schemeId = Crypt::decryptString($scheme_id);
                $this->schemeName = Scheme::where('id', $this->schemeId)->value('name');
            } catch (\Exception $e) {
                $this->schemeId = null;
            }
        }

    }

    // public function updatedSchemeId($value)
    // {
    //     $scheme = Scheme::find($value);
    //     $this->schemeName = $scheme?->name;
    // }
    public function updatedSchemeId($value)
    {
        $scheme = Scheme::find($value);
        $this->schemeName = $scheme?->name;

        $filter = $this->getFilterData();

        $result = SchemeCapacityHelper::check(
            $value,
            1,
            $filter
        );

        if (is_array($result)) {

            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => $result['message'],
            ]);

            $this->schemeId = null;
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
