<?php

namespace App\Validation\Tabs\Tab101;

use App\Validation\Tabs\BaseTabValidation;

/**
 * Master Class for Tab 101: Holds the DEFAULT rules for Tab 101.
 * 
 * If a scheme does not have custom requirements, it falls back to this class,
 * which serves default rules directly parsed from the scheme's JSON file.
 */
class MasterTab101Validation extends BaseTabValidation
{
    /**
     * Returns standard validation rules parsed from JSON schema for Tab 101.
     */
    public function getRules(): array
    {
        return $this->getJsonRules();
    }
}
