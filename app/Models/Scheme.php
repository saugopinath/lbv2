<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Scheme extends Model
{
     protected $fillable = [
            'name',
            'short_name',
            'description',
            'department_id'
        ];

     
    public function Department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    
  
    
}
