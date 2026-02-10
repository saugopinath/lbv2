<?php

namespace App\Services;

use App\Models\SchemeTabMapping;

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
            if (!$tab) {
                continue;
            }

            // prevent duplicate tab render
            if (in_array($tab->id, $processedTabIds)) {
                continue;
            }
            $processedTabIds[] = $tab->id;

            /**
             * =========================
             * COMPONENT TAB (104)
             * =========================
             */
            if ((int)$tab->tab_code === 104) {
                $tabs[] = [
                    'tab_name' => $tab->tab_name,
                    'type'     => 'component',
                ];
                continue;
            }

            /**
             * =========================
             * LOAD FIELD DEFINITIONS
             * =========================
             */
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

            /**
             * =========================
             * SINGLE ROW TABLE (IMPORTANT)
             * =========================
             */
            $record = $modelClass::where('application_id', $applicationId)->first();

            $rows = [];

            if ($record) {

                foreach ($fields as $field) {

                    /**
                     * ❌ Skip if db_column is NULL
                     */
                    if (empty($field->db_column)) {
                        continue;
                    }

                    $column = $field->db_column;
                    $value  = null;

                    /**
                     * =========================
                     * JSON COLUMN HANDLING
                     * =========================
                     */
                    if ($column === 'other_details') {

                        $jsonData = $record->other_details;

                        // if not casted
                        if (is_string($jsonData)) {
                            $jsonData = json_decode($jsonData, true);
                        }

                        $value = $jsonData[$field->field_name] ?? null;
                    } else {

                        $value = $record->$column ?? null;
                    }

                    $rows[] = [
                        'label' => $field->level_name
                            ?? $field->label
                            ?? ucfirst(str_replace('_', ' ', $field->field_name)),

                        'value' => $this->normalizeValue($value),
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

    private function normalizeValue($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        if (is_bool($value) || $value === 0 || $value === 1) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return collect($value)
                ->map(fn($v, $k) => ucfirst(str_replace('_', ' ', $k)) . ': ' . $v)
                ->implode(', ');
        }

        return (string) $value;
    }
}
