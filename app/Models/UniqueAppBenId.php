<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniqueAppBenId extends Model
{
    protected $table = 'lb_scheme.unique_app_ben_ids';
    protected $primaryKey = 'application_id';
    protected $fillable = ['application_id', 'beneficiary_id'];
}
