<?php

namespace App\Helpers;

use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;
use App\Models\Panchayat;
use App\Models\Ward;

class LocationHelper
{
    public static function resolve(
        string $fieldName,
        $value,
        ?int $ruralUrban = null
    ) {
        if ($value === null || $value === '') {
            return '-';
        }
        static $districtCache = [];
        static $blockCache = [];
        static $municipalityCache = [];
        static $gpCache = [];
        static $wardCache = [];

        return match ($fieldName) {
            'district_id' => $districtCache[$value]
                ??= District::where('id', $value)->value('name') ?? '-',
            'blockurban' => $ruralUrban == 2
                ? ($blockCache[$value]
                    ??= Block::where('id', $value)->value('name') ?? '-')
                : ($municipalityCache[$value]
                    ??= Municipality::where('id', $value)->value('name') ?? '-'),
            'gpWard' => $ruralUrban == 2
                ? ($gpCache[$value]
                    ??= Panchayat::where('id', $value)->value('name') ?? '-')
                : ($wardCache[$value]
                    ??= Ward::where('id', $value)->value('name') ?? '-'),
            'rural_urban' => $value == 2 ? 'Rural' : 'Urban',
            default => $value,
        };
    }
}
