<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class BeneficiaryPersonalDetail extends BaseAuditableModel
{
    use Searchable;
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

    public function creator()
    {
        $block = Block::where('lgd_code', $this->created_by_local_body_code)->first();
        if ($block) {
            return 1;
        }
        $subdivision = Subdivision::where('ref_code', $this->created_by_local_body_code)->first();
        if ($subdivision) {
            return 2;
        }
    }


    public function toSearchableArray()
    {
        $this->loadMissing('contact');

        return [
            'scheme_id' => $this->scheme_id,
            'application_id' => $this->application_id,
            'beneficiary_id' => $this->beneficiary_id,
            'application_type' => $this->application_type,
            'application_date' => $this->application_date,
            'ds_registration_no' => $this->ds_registration_no,
            'ds_date' => $this->ds_date,
            'beneficiary_name' => $this->beneficiary_name,
            'age' => $this->age,
            'email' => $this->email,
            'dob' => $this->dob,
            'ben_father_name' => $this->ben_father_name,
            'ben_mother_name' => $this->ben_mother_name,
            'mar_statu' => $this->mar_statu,
            'ben_spouse_name' => $this->ben_spouse_name,
            'caste' => $this->caste,
            'caste_cer_no' => $this->caste_cer_no,
            'next_level_role_id' => $this->next_level_role_id,
            'is_final' => $this->is_final,
            'created_by_dist_code' => $this->created_by_dist_code,
            'created_by_local_body_code' => $this->created_by_local_body_code,
            'other_details' => $this->other_details,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'is_clean' => $this->is_clean,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // From Relation
            'district_id' => $this->contact ? $this->contact->district_id : null,
            'rural_urban' => $this->contact ? $this->contact->rural_urban : null,
            'blockurban' => $this->contact ? $this->contact->blockurban : null,
            'gpward' => $this->contact ? $this->contact->gpward : null,
        ];
    }

    public function searchableAs()
    {
        return 'pension_beneficiary_personals';
    }
}
