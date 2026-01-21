<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanDetail extends Model
{
    protected $fillable = [
        'pan_no',
        'name',
        'address',
        'issue_date',
    ];
    protected $table = 'lb_scheme.pan_details';


    //
}
