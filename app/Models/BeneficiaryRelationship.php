<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BeneficiaryRelationship extends Model implements Auditable
{
<<<<<<< HEAD
    protected $guarded = [];
    protected $table = 'lb_scheme.beneficiary_relationships';
=======
    protected $table = 'lb_scheme.beneficiary_relationships';
    protected $primaryKey = 'application_id';

    // public function relative()
    // {
    //     return $this->belongsTo(BeneficiaryPersonal::class);
    // }
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
}
