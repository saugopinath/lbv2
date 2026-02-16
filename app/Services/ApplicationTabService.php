<?php

namespace App\Services;

use App\Models\SchemeTabMapping;
use App\Helpers\LocationHelper;

class ApplicationTabService
{
    public function getTabs($schemeId, $applicationId)
    {
        $mappings = SchemeTabMapping::with('masterTab')
            ->where('scheme_id', $schemeId)
            ->where('is_active', true)
            ->orderBy('position')
            ->get();
        $tabs = [];
        $processedTabIds = [];
        foreach ($mappings as $map) {
            $tab = $map->masterTab;
            if (!$tab || in_array($tab->id, $processedTabIds)) {
                continue;
            }
            $processedTabIds[] = $tab->id;
            if ((int) $tab->tab_code === 104) {
                $tabs[] = [
                    'tab_name' => $tab->tab_name,
                    'type'     => 'component',
                    'tab_code' => $tab->tab_code,
                ];
                continue;
            }
            $fields = $tab->getFields()
                ->where('scheme_id', $schemeId)
                ->values();
            if (!$tab->tab_model_name) {
                continue;
            }
            $modelClass = "App\\Models\\{$tab->tab_model_name}";
            if (!class_exists($modelClass)) {
                continue;
            }
            $record = $modelClass::where('application_id', $applicationId)->first();
            $rows = [];
            if ($record) {
                foreach ($fields as $field) {
                    if (empty($field->db_column)) {
                        continue;
                    }
                    if ($field->db_column === 'other_details') {
                        $jsonData = $record->other_details;
                        if (is_string($jsonData)) {
                            $jsonData = json_decode($jsonData, true);
                        }
                        $value = $jsonData[$field->field_name] ?? null;
                    } else {

                        $column = $field->db_column;
                        $value  = $record->$column ?? null;
                    }

                    // Skip if value is null or empty - don't show this field at all
                    if ($value === null || $value === '') {
                        continue;
                    }

                    $ruralUrban = $record->rural_urban ?? null;
                    $value = LocationHelper::resolve(
                        $field->db_column ?? $field->field_name,
                        $value,
                        $ruralUrban
                    );
                    $value = $this->mapOptionValue($field, $value);
                    $value = $this->normalizeValue($value);

                    $rows[] = [
                        'label' => $field->level_name
                            ?? $field->label
                            ?? ucfirst(str_replace('_', ' ', $field->field_name)),
                        'value' => $value,
                    ];
                }
            }

            $tabs[] = [
                'tab_name' => $tab->tab_name,
                'type'     => 'fields',
                'data'     => $rows,
            ];
        }
        return $tabs;
    }
    private function mapOptionValue($field, $value)
    {
        if ($value === null || empty($field->options)) {
            return $value;
        }
        $key = is_string($value) ? trim($value) : $value;
        return $field->options[$key] ?? $value;
    }
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
