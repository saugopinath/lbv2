<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleOfficeTypeMapping extends Model
{
    protected $fillable = [
            'office_type_id',
            'role_id'
        ];

    public function officeType()
    {
        return $this->belongsTo(Codemaster::class, 'office_type_id', 'code');
    }
    
  public function Role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}