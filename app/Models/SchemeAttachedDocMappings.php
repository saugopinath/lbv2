<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchemeAttachedDocMappings extends Model
{
    protected $table = 'scheme_attached_doc_mappings';

    protected $fillable = [
        'scheme_id',
        'doc_type_id',
        'tab_code',
        'is_required',
        'max_file_size',
        'extension_type',
        'field_position',
        'is_active',
    ];


    protected $casts = [
        'is_required' => 'boolean',
    ];
    public function codemaster()
    {
        return $this->belongsTo(Codemaster::class, 'doc_type_id');
    }
    public function docType()
    {
        return $this->belongsTo(Codemaster::class, 'doc_type_id');
    }
}
