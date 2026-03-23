<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class BeneficiaryEnclosure extends BaseAuditableModel
{
    use Searchable;
    protected $table = 'pension.beneficiary_documents';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $guarded = [];
    public function personal()
    {
        return $this->belongsTo(BeneficiaryPersonalDetail::class, 'application_id', 'application_id');
    }

    // public function documentType()
    // {
    //     return $this->belongsTo(Codemaster::class, 'document_type', 'id');
    // }

    public function documents()
    {
        return $this->belongsTo(Codemaster::class, 'document_type', 'id');
    }
    public function codemaster()
    {
        return $this->belongsTo(Codemaster::class, 'document_type', 'id');
    }
    public function searchableAs()
    {
        return 'pension_beneficiary_documents';
    }
    public function toSearchableArray()
    {
        return [
            'scheme_id' => $this->scheme_id,
            'beneficiary_id' => $this->beneficiary_id,
            'application_id' => $this->application_id,
            'attched_document' => $this->attched_document,
            'document_extension' => $this->document_extension,
            'document_mime_type' => $this->document_mime_type,
            'document_type' => $this->document_type,
            'created_by' => $this->created_by,
            'tab_code' => $this->tab_code,
            'is_clean' => $this->is_clean,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}