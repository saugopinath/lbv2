<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class Helper
{
    public static function getLgdFilters(): array
    {
        $lgd_session = session('lgd_session', []);

        $keys = ['block_id', 'district_id', 'subdivision_id'];

        $filters = [];
        foreach ($keys as $key) {
            $filters[$key] = isset($lgd_session[$key])
                ? Crypt::decryptString($lgd_session[$key])
                : null;
        }

        return $filters;
    }

}