<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryTemEnclosure extends Model
{
    use HasFactory;

    protected $table = 'beneficiary_tem_enclosures';

    protected $fillable = [
        'application_id',
        'document_type',
        'attched_document',
        'document_extension',
        'document_mime_type',
        'ip_address',
        'created_by',
    ];
}
