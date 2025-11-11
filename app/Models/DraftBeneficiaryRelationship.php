<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DraftBeneficiaryRelationship extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];
    protected $table = 'lb_scheme.draft_beneficiary_relationships';
    public function personal()
    {
        return $this->belongsTo(DraftBeneficiaryPersonal::class, 'application_id');
    }
    public static function getFullNameByCode($code)
    {
        $relationId = CodeMaster::getIdByCode($code);
        $relationship = self::where('relation_type_id', $relationId)->first();
        return $relationship ? $relationship->full_name : null;
    }
}
