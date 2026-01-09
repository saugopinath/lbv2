<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class BeneficiaryEnclosure extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'lb_scheme.beneficiary_enclosures';
    protected $guarded = [];
    public function personal()
    {
        return $this->belongsTo(DraftBeneficiaryPersonal::class, 'application_id');
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
