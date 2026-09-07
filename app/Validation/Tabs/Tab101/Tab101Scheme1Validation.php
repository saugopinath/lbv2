<?php

namespace App\Validation\Tabs\Tab101;

/**
 * Scheme 1 Specific Class for Tab 101.
 * 
 * Only create files like this when a specific scheme diverges from the default master rules.
 * Extends MasterTab101Validation so it inherits all default JSON rules,
 * allowing you to alter or add only what is necessary for Scheme 1.
 */
class Tab101Scheme1Validation extends MasterTab101Validation
{
    /**
     * Resolves consolidated rules specifically for Scheme 1 on Tab 101.
     */
    public function getRules(): array
    {
        // 1. Retrieve baseline rules from MasterTab101Validation
        $rules = parent::getRules();

        // // 2. Add or override rules specific to Scheme 1 on Tab 101
        // $rules['formData.application_type'] = ['required'];

        // // 3. Remove a field if Scheme 1 doesn't require it on Tab 101
        // unset($rules['formData.optional_pension_code']);

        return $rules;
    }
}
