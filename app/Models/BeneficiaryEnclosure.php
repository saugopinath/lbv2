<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;

class BeneficiaryEnclosure extends BaseAuditableModel
{
    use HasFactory, Searchable;

    protected $table = 'pension.beneficiary_documents';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_clean' => 'boolean',
    ];

    /**
     * Relation: Personal Details
     */
    public function personal()
    {
        return $this->belongsTo(BeneficiaryPersonalDetail::class, 'application_id', 'application_id');
    }

    /**
     * Relation: Codemaster (Document Type)
     */
    public function documentType()
    {
        return $this->belongsTo(Codemaster::class, 'document_type', 'id');
    }

    /**
     * Scout Index Name
     */
    public function searchableAs()
    {
        return 'pension_beneficiary_documents';
    }

    /**
     * Data for Meilisearch / Algolia
     */
    public function toSearchableArray()
    {
        return [
            'id' => (int) $this->id,
            'scheme_id' => $this->scheme_id,
            'beneficiary_id' => $this->beneficiary_id,
            'application_id' => $this->application_id,
            'attached_document' => $this->attched_document,
            'document_extension' => $this->document_extension,
            'document_mime_type' => $this->document_mime_type,
            'document_type' => $this->document_type,
            'created_by' => $this->created_by,
            'tab_code' => $this->tab_code,
            'storage_type' => $this->storage_type,
            'is_clean' => (bool) $this->is_clean,
            'created_at' => optional($this->created_at)->timestamp,
            'updated_at' => optional($this->updated_at)->timestamp,
        ];
    }
}
