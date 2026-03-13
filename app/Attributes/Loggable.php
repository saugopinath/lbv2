<?php

namespace App\Attributes;
use Attribute;
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Loggable
{
    public function __construct(
        public string $level = 'Normal',
        public ?string $nickname = null
    ) {}
}
