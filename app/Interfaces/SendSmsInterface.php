<?php

namespace App\Interfaces;

use App\Models\User;

interface SendSmsInterface
{

    public function sendSms(string $mobile_no,string $msg): bool;
    public function SmstrackInsert(int $userId,string $mobile_no,string $otp): bool;

}