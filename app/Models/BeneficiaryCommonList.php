<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class BeneficiaryCommonList extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];
    // protected $primaryKey = 'beneficiary_id';
    protected $table = 'lb_scheme.beneficiary_common_lists';
    public function sourceable()
    {
        return $this->morphTo();
    }
}
