<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class CheckAuthHelper
{
    public static function getRoleId(): ?int
    {
        return session('lgd_session') 
            ? (int) Crypt::decryptString(session('lgd_session.role_id')) 
            : null;
    }

    public static function isVerifier(): bool
    {
        return in_array(self::getRoleId(), [6, 7]);
    }

    public static function isOperator(): bool
    {
        return in_array(self::getRoleId(), [8, 9]);
    }

    public static function isApprover(): bool
    {
        return in_array(self::getRoleId(), [4, 5]);
    }
}
