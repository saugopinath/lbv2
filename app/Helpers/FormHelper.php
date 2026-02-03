<?php

namespace App\Helpers;

use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;
use App\Models\GpWard;
use App\Models\Panchayat;
use App\Models\Ward;
use App\Models\State;

class FormHelper
{
    public static function resolveValue(array $field, $value, $formData = [])
    {
        if ($value === null || $value === '') {
            return '-';
        }

        $type = $field['field_type'] ?? null;
        $fieldName = $field['field_name'] ?? null;

        /*
        --------------------------------
        SELECT OPTIONS (JSON)
        --------------------------------
        */
        if ($type === 'select' && !empty($field['options'])) {
            return $field['options'][$value] ?? '-';
        }

        /*
        --------------------------------
        CHECKBOX
        --------------------------------
        */
        if ($type === 'checkbox') {
            return $value ? 'Yes' : 'No';
        }

        /*
        ==================================
        SMART CACHE
        ==================================
        */
        static $districtCache = [];
        static $blockCache = [];
        static $municipalityCache = [];
        static $gpCache = [];
        static $wardCache = [];
        $ruralUrban = $formData['rural_urban'] ?? null;
        return match ($fieldName) {

            'district_id' =>
            $districtCache[$value]
                ??= District::where('id', $value)->value('name') ?? '-',
            'blockurban' =>
            $ruralUrban == 2
                ? ($blockCache[$value]
                    ??= Block::where('id', $value)->value('name') ?? '-')
                : ($municipalityCache[$value]
                    ??= Municipality::where('id', $value)->value('name') ?? '-'),

            'gpWard' =>
            $ruralUrban == 2
                ? ($gpCache[$value]
                    ??= Panchayat::where('id', $value)->value('name') ?? '-')
                : ($wardCache[$value]
                    ??= Ward::where('id', $value)->value('name') ?? '-'),
            'rural_urban' => $ruralUrban == 2 ? 'Rural' : 'Urban',
            default => $value
        };
    }
}
