<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class CasteModificationInfo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pension.caste_modification_infos';
    protected $guarded = ['id'];
    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];
    // public function beneficiaryCommonList()
    // {
    //     return $this->hasOne(BeneficiaryCommonList::class, 'sourceable_id', 'application_id');
    // }
    public function casteRequestType()
    {
        return $this->belongsTo(Codemaster::class, 'caste_request_type', 'id');
    }
    public function nextLevelRequested()
    {
        return $this->belongsTo(Codemaster::class, 'next_level_requested_id', 'id');
    }
}
