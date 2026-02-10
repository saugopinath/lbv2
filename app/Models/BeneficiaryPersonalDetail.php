<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BeneficiaryPersonalDetail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
     protected $guarded = [];
    protected $table = 'lb_scheme.beneficiary_personal_details';

    protected $casts = [
        'other_details' => 'array',
    ];

    public function casteName()
    {
        return $this->belongsTo(CodeMaster::class, 'caste', 'id');
    }    

    public function documents()
    {
        return $this->hasMany(BeneficiaryEnclosure::class, 'application_id');
    }

   
    public function getStatusText(): string
    {
        if ($this->next_level_role_id == Codemaster::getIdByCode(22)) {
            return 'Submitted but Verification Pending';
        } elseif ($this->next_level_role_id == Codemaster::getIdByCode(23)) {
            return 'Verified but Approval Pending';
        } else {
            return 'Partially Submitted';
        }
    }   
}
