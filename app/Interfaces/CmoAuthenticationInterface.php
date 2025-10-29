<?php

namespace App\Interfaces;


interface CmoAuthenticationInterface
{
    public function generateOTP();
    public function authiticated();
    public function pullNewCmo($from_date, $to_date);
}
