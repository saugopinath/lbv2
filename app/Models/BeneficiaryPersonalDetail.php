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
    public function bank()
    {
        return $this->hasOne(BeneficiaryBankDetail::class, 'beneficiary_id', 'beneficiary_id');
    }

    public function aadhar()
    {
        return $this->hasOne(BeneficiaryAadhaar::class, 'beneficiary_id', 'beneficiary_id');
    }

    public function getStatusText()
    {
        $nextRoleId = $this->next_level_role_id;
        $schemeId = $this->scheme_id;
        if (is_null($nextRoleId)) {
            return "Partially Entry";
        }
        if ($nextRoleId < 0) {
            return ($nextRoleId == -100) ? "Rejected" : "Reverted";
        }
        $mapping = WorkflowsteproleMapping::where('scheme_id', $schemeId)
            ->where('next_label_role_id', $nextRoleId)
            ->with('workflowStep')
            ->first();
        if (!$mapping || !$mapping->workflowStep) {
            return "Unknown Status";
        }
        $step = $mapping->workflowStep;
        if ($step->is_first) {
            return "Verification Pending";
        }
        if ($step->is_last) {
            return "Approved";
        }
        return "Approval Pending";
    }
    public function getStatusBadge()
    {
        $status = $this->getStatusText();
        return match ($status) {
            'Approved'             => "bg-green-100 text-green-700 border-green-300",
            'Verification Pending' => "bg-blue-100 text-blue-800 border-blue-300",
            'Approval Pending'     => "bg-indigo-100 text-indigo-800 border-indigo-300",
            'Partially Entry'      => "bg-cyan-100 text-cyan-700 border-cyan-300",
            'Reverted'             => "bg-yellow-100 text-yellow-700 border-yellow-300",
            'Rejected'             => "bg-red-100 text-red-700 border-red-300",
            default                => "bg-gray-100 text-gray-700 border-gray-300",
        };
    }
}
