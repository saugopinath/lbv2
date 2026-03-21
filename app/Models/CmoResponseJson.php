<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class CmoResponseJson extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'cmo.cmo_response_json';
    public $timestamps = false;
    protected $guarded = [];
}
