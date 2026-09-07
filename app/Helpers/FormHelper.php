<?php

namespace App\Helpers;

use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;
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
        if ($type === 'select' && !empty($field['options'])) {
            return $field['options'][$value] ?? '-';
        }
        if ($type === 'checkbox') {
            if (!empty($field['is_multiple']) && is_array($value)) {
                $labels = [];
                foreach ($value as $v) {
                    if (isset($field['options'][$v])) {
                        $labels[] = $field['options'][$v];
                    }
                }
                return empty($labels) ? '-' : implode(', ', $labels);
            }
            return $value ? 'Yes' : 'No';
        }
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

            'gpward' =>
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
