<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\Auth;

class BeneficiaryPersonalDetail extends BaseAuditableModel
{
    protected $guarded = [];
    protected $table = 'lb_scheme.beneficiary_personal_details';

    protected $casts = [
        'other_details' => 'array',
    ];


    public function contact()
    {
        return $this->hasOne(BeneficiaryContactDetail::class, 'beneficiary_id', 'beneficiary_id');
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
