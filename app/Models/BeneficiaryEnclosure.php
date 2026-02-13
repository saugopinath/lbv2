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
}
