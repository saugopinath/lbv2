<?php

namespace App\Traits;

trait WithLiveValidation
{
    /**
     * Automatically validate a field when it is updated.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
}
