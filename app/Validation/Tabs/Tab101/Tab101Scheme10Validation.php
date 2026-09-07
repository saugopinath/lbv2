<?php

namespace App\Validation\Tabs\Tab101;

class Tab101Scheme10Validation extends MasterTab101Validation
{
    public function getRules(): array
    {
        $rules = parent::getRules();
        // $rules['formData.application_type'] = ['required'];
        // unset($rules['formData.beneficiary_name']);
        return $rules;
    }
}
