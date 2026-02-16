<?php

namespace App\Models;


class BeneficiaryPersonalDetail extends BaseAuditableModel
{
    protected $guarded = [];
    protected $table = 'pension.beneficiary_personals';
    protected $primaryKey = 'application_id';
    public $incrementing = false;

    protected $casts = [
        'other_details' => 'array',
    ];


    public function contact()
    {
        return $this->hasOne(BeneficiaryContactDetail::class, 'application_id', 'application_id');
    }
    public function documents()
    {
        return $this->hasMany(BeneficiaryEnclosure::class, 'application_id');
    }

    // public function transformAudit(array $data): array
    // {
    //     $data['new_values']['updated_by_role'] = Auth::user()->role_id;
    //     $data['new_values']['session_id'] = session()->getId();
    //     $data['new_values']['user_agent'] = \Illuminate\Support\Facades\Request::userAgent();
    //     $data['new_values']['url'] = \Illuminate\Support\Facades\Request::fullUrl();
    //     $data['new_values']['method'] = \Illuminate\Support\Facades\Request::method();
    //     $data['new_values']['referrer'] = \Illuminate\Support\Facades\Request::header('referer');
    //     return $data;
    // }
}