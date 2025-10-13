<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BenRejectDetail extends Model
{
    protected $table = 'lb_scheme.ben_reject_details';
    protected $primaryKey = 'application_id';

    public $timestamps = false;
}
