<?php

namespace App\Models;

use Laravel\Scout\Searchable;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryContactDetail extends BaseAuditableModel
{
    use Searchable;
    protected $table = "pension.beneficiary_contacts";
    protected $primaryKey = 'application_id';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'other_details' => 'array',
    ];

    public function personal()
    {
        return $this->belongsTo(BeneficiaryPersonalDetail::class, 'application_id', 'application_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'blockurban', 'id');
    }
    public function searchableAs()
    {
        return 'pension_beneficiary_contacts';
    }
    public function toSearchableArray()
    {
        return [
            'scheme_id' => $this->scheme_id,
            'application_id' => $this->application_id,
            'beneficiary_id' => $this->beneficiary_id,
            'state' => $this->state,
            'district_id' => $this->district_id,
            'rural_urban' => $this->rural_urban,
            'blockurban' => $this->blockurban,
            'gpward' => $this->gpward,
            'policestation' => $this->policestation,
            'villtowncity' => $this->villtowncity,
            'housepremiseno' => $this->housepremiseno,
            'postoffice' => $this->postoffice,
            'pincode' => $this->pincode,
            'other_details' => $this->other_details,
            'is_clean' => $this->is_clean,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
