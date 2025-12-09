<?php

namespace App\Interfaces;

interface JnmpAuthenticationInterface
{
    public function getJnmpData($data);
    public function detailsCallBack($data);
}
