<?php

namespace App\Validation\Tabs\Tab107;

use App\Validation\Tabs\BaseTabValidation;

class MasterTab107Validation extends BaseTabValidation
{
    public function getRules(): array
    {
        return $this->getJsonRules();
    }
}
