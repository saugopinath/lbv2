<?php

namespace App\Interfaces;


interface AuthenticationInterface
{

    public function credentialCheck(string $mobile_no,string $password): array;
    public function userLastOtpStore(int $userid, string $otp): bool;
   

}