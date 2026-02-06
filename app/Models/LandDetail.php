<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandDetail extends Model
{
protected $casts = ['other_details' => 'array'];


    protected $guarded = [];
protected $table = 'lb_scheme.land_details';


    //
}
