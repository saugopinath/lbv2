<?php

namespace App\Services;

use App\Models\BSKUserDutyMapping;
use Illuminate\Support\Facades\Auth;

class BskAuthService
{
    /**
     * Web session login (after ticket verification)
     */
    // public function sessionLogin(BSKUserDutyMapping $user): void
    // {
    //     Auth::guard('bsk_session')->login($user);
    // }

    /**
     * API JWT login
     */
    public function jwtLogin(array $credentials): string|false
    {
        return Auth::guard('bsk')->attempt($credentials);
    }
}
