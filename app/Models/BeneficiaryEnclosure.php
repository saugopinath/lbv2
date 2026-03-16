<?php

namespace App\Models;

class BeneficiaryEnclosure extends BaseAuditableModel
{
    protected $table = 'pension.beneficiary_documents';
    protected $primaryKey = 'application_id';
    public $incrementing = false;
    protected $guarded = [];
    public function personal()
    {
        return $this->belongsTo(BeneficiaryPersonalDetail::class, 'application_id');
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
