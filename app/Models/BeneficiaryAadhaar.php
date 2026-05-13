<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class BeneficiaryAadhaar extends BaseAuditableModel
{
    use Searchable;
    protected $guarded = [];
    protected $primaryKey = 'application_id';
    protected $table = 'pension.beneficiary_aadhaars';
    public $incrementing = false;
    public function personal()
    {
        return $this->belongsTo(BeneficiaryPersonalDetail::class, 'application_id', 'application_id');
    }
    public function searchableAs()
    {
        return 'pension_beneficiary_aadhars';
    }
    public function toSearchableArray()
    {
        return [
            'application_id' => $this->application_id,
            'beneficiary_id' => $this->beneficiary_id,
            'encode_key' => $this->encode_key,
            'encoded_aadhaar' => $this->encoded_aadhaar,
            'aadhaar_vault' => $this->aadhaar_vault,
            'aadhaar_hash' => $this->aadhaar_hash,
            'is_clean' => $this->is_clean,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'scheme_id' => $this->scheme_id,
        ];
    }
}
