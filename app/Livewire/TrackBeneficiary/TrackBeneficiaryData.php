<?php

namespace App\Livewire\TrackBeneficiary;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

use App\Models\BeneficiaryPersonalDetail;

use Illuminate\Support\Facades\Crypt;

class TrackBeneficiaryData extends Component
{
    use WithPagination;

    public $search = '';
    public $scheme = '';
    public $district = '';
    public $urban_code = '';
    public $block = '';
    public $gp_ward = '';

    public $schemes = [];
    public $filter_condition = [];

    public function updating()
    {
        $this->resetPage();
    }

    public function searchBeneficiary()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $select_lgd = session('lgd_session');

        if (!empty($select_lgd['district_id'])) {

            $this->filter_condition['created_by_dist_code']
                = Crypt::decryptString(
                    $select_lgd['district_id']
                );
        }

        if (!empty($select_lgd['scheme_id'])) {

            $schemeRaw = is_array($select_lgd['scheme_id'])
                ? $select_lgd['scheme_id']
                : [$select_lgd['scheme_id']];

            $this->schemes = array_map(
                fn($id) => Crypt::decryptString($id),
                $schemeRaw
            );
        }
    }

    public function render()
    {

        $query = BeneficiaryPersonalDetail::search(
            trim($this->search)
        );
        if (!empty($this->schemes)) {

            $query->whereIn(
                'scheme_id',
                $this->schemes
            );
        }
        foreach ($this->filter_condition as $key => $value) {

            $query->where($key, $value);
        }
        if ($this->scheme) {

            $query->where(
                'scheme_id',
                (int) $this->scheme
            );
        }
        if ($this->urban_code) {
            $query->where(
                'rural_urban',
                (int) $this->urban_code
            );
        }
        if ($this->block) {
            $query->where(
                'blockurban',
                (int) $this->block
            );
        }
        if ($this->gp_ward) {

            $query->where(
                'gpward',
                (int) $this->gp_ward
            );
        }

        $beneficiaries = $query
            ->paginate(20);

        return view(
            'livewire.track-beneficiary.track-beneficiary-data',
            [
                'beneficiaries' => $beneficiaries
            ]
        );
    }
}
