<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchemeAttachedDocMappings extends Model
{
    public function codemaster()
    {
        return $this->belongsTo(Codemaster::class, 'doc_type_id');
    }
}
