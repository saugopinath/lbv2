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

    public function personaldetails()
    {
        return $this->belongsTo(BeneficiaryPersonalDetail::class, 'application_id');
    }
    public function contactdetails()
    {
        return $this->belongsTo(BeneficiaryContactDetail::class, 'application_id');
    }
    public function bankdetails()
    {
        return $this->belongsTo(BeneficiaryBankDetail::class, 'application_id');
    }
    // public function aadhar()
    // {
    //     return $this->belongsTo(BeneficiaryAadhaar::class, 'application_id');
    // }
    public function incompletType()
    {
        return $this->belongsTo(Codemaster::class, 'incomplet_type', 'code');
    }
    public function enclosers()
    {
        return $this->hasMany(BeneficiaryEnclosure::class, 'application_id', 'application_id');
    }
    public function incompleteType()
    {
        return $this->belongsTo(IncompletTypeModelMapping::class, 'incomplet_type', 'incomplet_type_code');
    }

    public function contact()
    {
        return $this->hasOne(BeneficiaryContactDetail::class, 'application_id','application_id');
    }
    public function documents()
    {
        return $this->hasMany(BeneficiaryEnclosure::class, 'application_id');
    }
    public function aadhaar()
    {
        return $this->hasOne(BeneficiaryAadhaar::class, 'application_id');
    }
    public function bank()
    {
        return $this->hasOne(BeneficiaryBankDetail::class, 'application_id', 'application_id');
    }
      public function failedPaymentDetails()
    {
        return $this->hasOne(FailedPaymentDetails::class, 'ben_id', 'beneficiary_id');
    }
     public function benPaymentDetails()
    {
        return $this->hasOne(BenPaymentDetails::class, 'ben_id', 'beneficiary_id');
    }

    public function scopeApplicationWise($query, $schemeId = null)
    {
        if ($schemeId) {
            $query->where('scheme_id', $schemeId);
        }

        return $query
            ->selectRaw('MIN(id) as id, application_id')
            ->groupBy('application_id')
            ->with([
                'personaldetails',
                'contactdetails.district',
                'contactdetails.block',
                'contactdetails.panchayat',
                'contactdetails.ward',
            ])
            ->orderBy('application_id', 'asc');
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
    public function acceptRejectInfo()
    {
        return $this->hasOne(AcceptRejectInfo::class, 'application_id', 'application_id');
    }
    public function banks()
    {
        return $this->hasOne(BeneficiaryBankDetail::class, 'application_id', 'application_id');
    }
}
