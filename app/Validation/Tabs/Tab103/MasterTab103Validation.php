<?php

namespace App\Validation\Tabs\Tab103;

use App\Validation\Tabs\BaseTabValidation;

class MasterTab103Validation extends BaseTabValidation
{
    public function getRules(): array
    {
        return $this->getJsonRules();
    }
}
