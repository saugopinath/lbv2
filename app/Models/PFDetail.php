<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PFDetail extends Model
{
protected $casts = ['other_details' => 'array'];


    protected $guarded = [];
protected $table = 'pension.p_f_details';


    //
}
