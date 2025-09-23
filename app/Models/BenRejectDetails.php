<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BenRejectDetails extends Model
{
    // protected $guarded = [
    //     'id',
    // ];

    protected $fillable = [
        'application_id',
        'beneficiary_id',
        'created_by',
        'personal_details',
        'contact_details',
        'bank_details',
        'declaration_details',
        'relationship_details',
        'aadhar_details',
        'district_id',
        'block_id',
        'sub_division_id',
        'municipality_id',
        'ward_id',
        'panchayat_id',
    ];


    protected $table = 'lb_scheme.ben_reject_details';
    protected $casts = [
        'personal_details' => 'array',
        'contact_details' => 'array',
        'bank_details' => 'array',
        'declaration_details' => 'array',
        'relationship_details' => 'array',
        'aadhar_details' => 'array',
    ];
    public $update_code;


    public function lists()
    {
        return $this->morphOne(BeneficiaryCommonList::class, 'sourceable');
    }
    
    protected static function booted()
    {
        static::created(function ($benRejectDetails) {
            if ($benRejectDetails) {
                $benRejectDetails->lists()->update([]);
            }
        });
    }
}
