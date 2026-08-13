<?php

namespace App\Services;

use App\Models\SchemeTabMapping;
use App\Helpers\LocationHelper;

class ApplicationTabWiseService
{
    /* -------------------------------------------------
     |  TAB META (Only tab list – no heavy query)
     -------------------------------------------------*/
    public function getTabsMeta($schemeId, array $allowedTabCodes = [])
    {
        return SchemeTabMapping::with('masterTab')
            ->where('scheme_id', $schemeId)
            ->where('is_active', true)
            ->orderBy('position')
            ->get()
            ->map(function ($map) use ($allowedTabCodes) {

                $tab = $map->masterTab;
                if (!$tab) return null;

                // tab_code filter
                if (
                    !empty($allowedTabCodes) &&
                    !in_array((int)$tab->tab_code, $allowedTabCodes)
                ) {
                    return null;
                }

                return [
                    'tab_name' => $tab->tab_name,
                    'tab_code' => (int)$tab->tab_code,
                    'type'     => (int)$tab->tab_code === 104 ? 'component' : 'fields',
                    'loaded'   => false,
                    'data'     => [],
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }

    /* -------------------------------------------------
     |  SINGLE TAB DATA (Lazy Load)
     -------------------------------------------------*/
    public function getTabData($schemeId, $applicationId, $tabCode)
    {
        $map = SchemeTabMapping::with('masterTab')
            ->where('scheme_id', $schemeId)
            ->whereHas(
                'masterTab',
                fn($q) =>
                $q->where('tab_code', $tabCode)
            )
            ->first();

        if (!$map || !$map->masterTab) return [];

        $tab = $map->masterTab;

        if (!$tab->tab_model_name) return [];

        $modelClass = "App\\Models\\{$tab->tab_model_name}";
        if (!class_exists($modelClass)) return [];

        $record = $modelClass::where('application_id', $applicationId)->first();
        if (!$record) return [];

        $rows = [];

        foreach ($tab->getFields()->where('scheme_id', $schemeId) as $field) {

            if (!$field->db_column) continue;

            /* ----------- VALUE FETCH ----------- */
            if ($field->db_column === 'other_details') {
                $json = $record->other_details;
                if (is_string($json)) {
                    $json = json_decode($json, true);
                }
                $value = $json[$field->field_name] ?? null;
            } else {
                $value = $record->{$field->db_column} ?? null;
            }

            // hide empty field
            if ($value === null || $value === '') continue;

            /* ----------- OPTION MAPPING ----------- */
            $value = $this->mapOptionValue($field, $value);

            /* ----------- LOCATION / MASTER VALUE ----------- */
            $value = LocationHelper::resolve(
                $field->db_column ?? $field->field_name,
                $value,
                $record->rural_urban ?? null
            );

            /* ----------- NORMALIZE ----------- */
            $value = $this->normalizeValue($value);

            $rows[] = [
                'label' => $field->level_name
                    ?? $field->label
                    ?? ucfirst(str_replace('_', ' ', $field->field_name)),
                'value' => $value,
            ];
        }

        return $rows;
    }

    /* -------------------------------------------------
     |  OPTION VALUE MAP (ID → NAME)
     -------------------------------------------------*/
    private function mapOptionValue($field, $value)
    {
        if ($value === null || empty($field->options)) {
            return $value;
        }

        // options column should be json / array
        $options = is_string($field->options)
            ? json_decode($field->options, true)
            : $field->options;

        $key = is_string($value) ? trim($value) : $value;

        return $options[$key] ?? $value;
    }

    /* -------------------------------------------------
     |  NORMALIZE VALUE
     -------------------------------------------------*/
    private function normalizeValue($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if ($value === 0 || $value === 1) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return collect($value)
                ->map(
                    fn($v, $k) =>
                    ucfirst(str_replace('_', ' ', $k)) . ': ' . $v
                )
                ->implode(', ');
        }

        return (string) $value;
    }
}
