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
                'fields'   => $fields,
            ];
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
    public static function store(int $schemeId, array $tabs): string
    {
        $dir = resource_path("views/schemes/scheme_{$schemeId}");

        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        foreach ($tabs as $tab) {

            $blade = "<div class=\"grid md:grid-cols-2 gap-4 mt-4\">\n";

            foreach ($tab['fields'] as $field) {

                /* ---------------- NORMAL FORM TABS ---------------- */
                if (!in_array($tab['tab_code'], [104, 105])) {

                    $label = $field['level_name'] ?? '';
                    $name  = $field['field_name']
                        ?? $field['field_id']
                        ?? 'field_' . ($field['tab_field_id'] ?? uniqid());

                    $type  = $field['field_type'] ?? 'text';

                    $blade .= "<div>\n";
                    $blade .= "<label class=\"block mb-2 text-sm font-medium\">{$label}</label>\n";

                    switch ($type) {
                        case 'text':
                            $blade .= "<input type=\"text\" name=\"{$name}\" class=\"border rounded-lg w-full p-2.5\" />\n";
                            break;

                        case 'date':
                            $blade .= "<input type=\"date\" name=\"{$name}\" class=\"border rounded-lg w-full p-2.5\" />\n";
                            break;

                        case 'select':
                            $blade .= "<select name=\"{$name}\" class=\"border rounded-lg w-full p-2.5\">\n";
                            $blade .= "<option value=\"\">-- Select --</option>\n";
                            foreach ($field['options'] ?? [] as $opt) {
                                $blade .= "<option>{$opt}</option>\n";
                            }
                            $blade .= "</select>\n";
                            break;
                    }

                    $blade .= "</div>\n";
                }

                /* ---------------- DOCUMENT TAB (104) ---------------- */
                if ($tab['tab_code'] == 104) {

                    $label = $field['doc_type_name'] ?? 'Document';
                    $name  = $field['doc_type']['short_name']
                        ?? 'doc_' . ($field['doc_type_code'] ?? uniqid());

                    $accept = isset($field['extension_type'])
                        ? str_replace(',', ',.', '.' . $field['extension_type'])
                        : '';

                    $required = ($field['is_required'] ?? false) ? 'required' : '';

                    $blade .= "<div>\n";
                    $blade .= "<label class=\"block mb-2 text-sm font-medium\">{$label}</label>\n";
                    $blade .= "<input type=\"file\" name=\"{$name}\" {$required}
                            accept=\"{$accept}\"
                            class=\"border rounded-lg w-full p-2.5\" />\n";
                    $blade .= "</div>\n";
                }

                /* ---------------- DECLARATION TAB (105) ---------------- */
                if ($tab['tab_code'] == 105) {

                    $label = $field['level_name'] ?? 'Declaration';
                    $name  = 'declaration_' . ($field['id'] ?? uniqid());

                    $blade .= "<div class=\"md:col-span-2\">\n";
                    $blade .= "<label class=\"inline-flex items-center\">\n";
                    $blade .= "<input type=\"checkbox\" name=\"{$name}\" class=\"mr-2\" /> {$label}\n";
                    $blade .= "</label>\n</div>\n";
                }
            }

            $blade .= "</div>";

            File::put(
                $dir . "/{$tab['tab_code']}.blade.php",
                $blade
            );
        }

        return $dir;
    }
}
