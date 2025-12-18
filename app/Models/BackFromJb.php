<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackFromJb extends Model
{
    protected $table = 'lb_scheme.back_from_jbs';
    protected $primaryKey = 'application_id';
    public function beneficiary()
    {
        return $this->hasOne(BeneficiaryCommonList::class, 'sourceable_id', 'application_id');
    }
}
