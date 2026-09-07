<?php

namespace App\Validation\Tabs\Tab105;

use App\Validation\Tabs\BaseTabValidation;

class MasterTab105Validation extends BaseTabValidation
{
    public function getRules(): array
    {
        return $this->getJsonRules();
    }
}
