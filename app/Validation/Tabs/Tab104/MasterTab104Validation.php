<?php

namespace App\Validation\Tabs\Tab104;

use App\Validation\Tabs\BaseTabValidation;

class MasterTab104Validation extends BaseTabValidation
{
    public function getRules(): array
    {
        return $this->getJsonRules();
    }
}
