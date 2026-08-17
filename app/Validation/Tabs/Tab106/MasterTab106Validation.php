<?php

namespace App\Validation\Tabs\Tab106;

use App\Validation\Tabs\BaseTabValidation;

class MasterTab106Validation extends BaseTabValidation
{
    public function getRules(): array
    {
        return $this->getJsonRules();
    }
}
