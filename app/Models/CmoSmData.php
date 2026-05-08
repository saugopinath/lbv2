<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use OwenIt\Auditing\Contracts\Auditable;
class CmoSmData  extends BaseAuditableModel
{
    // use \OwenIt\Auditing\Auditable;
    protected $table = 'cmo.cmo_sm_data';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'grievance_id';
}
