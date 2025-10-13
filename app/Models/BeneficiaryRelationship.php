<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BeneficiaryRelationship extends Model implements Auditable
{
    protected $table = 'lb_scheme.beneficiary_relationships';
    protected $primaryKey = 'application_id';

    // public function relative()
    // {
    //     return $this->belongsTo(BeneficiaryPersonal::class);
    // }
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];
}
