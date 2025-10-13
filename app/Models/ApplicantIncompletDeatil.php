<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class ApplicantIncompletDeatil extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'applicant_incomplet_deatils';
    protected $fillable = [
        'application_id',
        'beneficiary_id',
        'incomplet_type',
        'next_level_request_id',
        'new_value',
        'old_value',
        'request_id',
        'is_active',
        'change_type',
    ];

    protected $casts = [
        'new_value' => 'array',
        'old_value' => 'array',
    ];

    public function commonList()
    {
        return $this->belongsTo(BeneficiaryCommonList::class, 'application_id', 'sourceable_id');
    }
    public function incompletType()
    {
        return $this->belongsTo(Codemaster::class, 'incomplet_type', 'code');
    }

    public function incompleteType()
    {
        return $this->belongsTo(IncompletTypeModelMapping::class, 'incomplet_type', 'incomplet_type_code');
    }

    public function getIncompleteTypesNamesAttribute()
    {
        return ApplicantIncompletDeatil::where('application_id', $this->application_id)
            ->whereIn('is_active', [0, 1])
            ->with('incompletType')
            ->get()
            ->pluck('incompletType.name')
            ->filter()
            ->values()
            ->map(function ($name, $index) {
                return ($index + 1) . '. ' . $name;
            })
            ->implode('<br>');
    }

    public function beneficiaryCommonList()
    {
        return $this->hasOne(BeneficiaryCommonList::class, 'sourceable_id', 'application_id');
    }

    public function acceptRejectInfo()
    {
        return $this->hasOne(AcceptRejectInfo::class, 'application_id', 'application_id');
    }
    public function banks()
    {
        return $this->hasOne(BeneficiaryBank::class, 'application_id', 'application_id');
    }
}
