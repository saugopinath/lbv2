<?php

namespace App\Validation\Tabs\Tab102;

use App\Validation\Tabs\BaseTabValidation;

class MasterTab102Validation extends BaseTabValidation
{
    public function getRules(): array
    {
        return $this->getJsonRules();
    }
}
