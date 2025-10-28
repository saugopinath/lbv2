<?php

namespace App\Interfaces;


interface CmoAuthenticationInterface
{

    public function generateOTP();
    public function authiticated();
   

}