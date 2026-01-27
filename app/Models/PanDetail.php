<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanDetail extends Model
{
    protected $fillable = [        'name',
        'pan_no',
        'address',
        'issue_from',
        'is_expire',
        'issue_date',];
    protected $table = 'lb_scheme.pan_details';


    //
}
