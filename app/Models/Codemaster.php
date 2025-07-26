<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Codemaster extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'parent_id',
    ];
    public function parent()
    {
        return $this->belongsTo(Codemaster::class, 'parent_id');
    }
}
