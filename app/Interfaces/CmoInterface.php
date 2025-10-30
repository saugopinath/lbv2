<?php

namespace App\Interfaces;


interface CmoInterface
{
    public function generateOTP();
    public function authiticated();
    public function pullNewCmo($from_date, $to_date);
}
