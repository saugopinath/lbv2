<?php

namespace App\Helpers;

use App\Models\SchemeAttachedDocMappings;
use App\Models\SchemeTabBasefield;
use App\Models\SchemeTabMapping;
use App\Models\SchemeTabFormField;
use App\Models\SelfDeclerationBasefield;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SchemewiseStoreDataJsonHelper
{
    public static function generateSchemeJson(int $schemeId): array
    {
        $tabs = SchemeTabMapping::with('masterTab')
            ->where('scheme_id', $schemeId)
            ->where('is_active', true)
            ->orderBy('position')
            ->get();

        $tabData = [];
        foreach ($tabs as $tab) {
            $model = match ($tab->tab_code) {
                105     => SelfDeclerationBasefield::class,
                104     => SchemeAttachedDocMappings::class,
                default => SchemeTabFormField::class,
            };
            if ($tab->tab_code == 104) {
                $fields = $model::with('docType')
                    ->where('scheme_id', $schemeId)
                    ->where('tab_code', $tab->tab_code)
                    ->where('is_active', true)
                    ->orderBy('field_position')
                    ->get()
                    ->map(function ($field) {
                        $data = $field->toArray();
                        $data['doc_type_name'] = $field->docType?->name;
                        $data['doc_type_code'] = $field->docType?->code ?? null;
                        return $data;
                    })
                    ->toArray();
            } else {
                $fields = $model::where('scheme_id', $schemeId)
                    ->where('tab_code', $tab->tab_code)
                    ->where('is_active', true)
                    ->orderBy('field_position')
                    ->get()
                    ->toArray();
            }
            $tabData[] = [
                'tab_code' => $tab->tab_code,
                'tab_name' => $tab->masterTab->tab_name ?? '',
                'tab_icon' => $tab->masterTab->tab_icon ?? '',
                'tab_short_name' => $tab->masterTab->tab_short_name ?? '',
                'fields'   => $fields,
            ];
            // $fields = $model::where('scheme_id', $schemeId)
            //     ->where('tab_code', $tab->tab_code)
            //     ->where('is_active', true)
            //     ->orderBy('field_position')
            //     ->get()
            //     ->map(fn ($field) => [
            //         'id'                 => $field->id,
            //         'tab_field_id'      => $field->tab_field_id ?? null,
            //         'field_name'         => $field->field_name ?? null,
            //         'level_name'         => $field->level_name,
            //         'field_type'         => $field->field_type,
            //         'validation_rule'    => $field->validation_rule,
            //         'regex'              => $field->regex ?? null,
            //         'is_mandatory'       => $field->is_mandatory ?? null,
            //         'section_level_id'   => $field->section_level_id ?? null,
            //         'section_level_type' => $field->section_level_type ?? null,
            //         'options'            => $field->options ?? [],
            //         'is_multiple'      => $field->is_multiple ?? false,
            //         'db_column'        => $field->db_column ?? null,
            //         'is_active'          => $field->is_active ?? null,
            //         'field_position'     => $field->field_position ?? null,
            //         'tab_code'           => $field->tab_code ?? null,
            //         'scheme_id'          => $field->scheme_id ?? null,
            //         'created_by'         => $field->created_by ?? null,
            //         'updated_by'         => $field->updated_by ?? null,

            //     ])
            //     ->toArray();
            // $fields = $model::where('scheme_id', $schemeId)
            //     ->where('tab_code', $tab->tab_code)
            //     ->where('is_active', true)
            //     ->orderBy('field_position')
            //     ->get()
            //     ->toArray();
            // $tabData[] = [
            //     'tab_code' => $tab->tab_code,
            //     'tab_name' => $tab->masterTab->tab_name ?? '',
            //     'fields'   => $fields,
            // ];
        }
        return [
            'scheme_id'    => $schemeId,
            'generated_at' => now()->toDateTimeString(),
            'tabs'         => $tabData,
        ];
    }

    public static function storeSchemeJson(int $schemeId, array $data): string
    {
        $path = "final_schemes_formdata/scheme_{$schemeId}.json";
        Storage::disk('local')->put(
            $path,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        return $path;
    }

    public static function checkMandatoryBaseFields(int $schemeId): array
    {
        $mandatoryBaseFields = SchemeTabBasefield::where('is_mendetory', 1)
            ->pluck('id')
            ->toArray();

        $configuredFields = SchemeTabFormField::where('scheme_id', $schemeId)
            ->where('is_active', true)
            ->pluck('tab_field_id')
            ->toArray();

        $missingFields = array_diff($mandatoryBaseFields, $configuredFields);
        $missingFieldNames = SchemeTabBasefield::whereIn('id', $missingFields)
            ->pluck('level_name')
            ->toArray();
        return $missingFieldNames;
    }

    // public static function validateSchemeData(int $schemeId): bool
    // {
    //     $tabs = SchemeTabMapping::where('scheme_id', $schemeId)
    //         ->where('is_active', true)
    //         ->get();
    //     foreach ($tabs as $tab) {
    //         $model = match ($tab->tab_code) {
    //             105     => SelfDeclerationBasefield::class,
    //             default => SchemeTabFormField::class,
    //         };
    //         $fields = $model::where('scheme_id', $schemeId)
    //             ->where('tab_code', $tab->tab_code)
    //             ->where('is_active', true)
    //             ->get();
    //         foreach ($fields as $field) {
    //             if ($field->is_mandatory && empty($field->validation_rule)) {
    //                 return false;
    //             }
    //         }
    //     }

    //     return true;
    // }
}
