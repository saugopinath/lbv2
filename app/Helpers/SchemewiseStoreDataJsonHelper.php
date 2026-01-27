<?php

namespace App\Helpers;

use App\Models\SchemeAttachedDocMappings;
use App\Models\SchemeTabBasefield;
use App\Models\SchemeTabMapping;
use App\Models\SchemeTabFormField;
use App\Models\SelfDeclerationBasefield;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\SchemeTabLayout;
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
            /** ================= LAYOUT ================= */

            $schemeTabLayout = SchemeTabLayout::where('scheme_id', $schemeId)
                ->where('tab_code', $tab->tab_code)
                ->first();

            $layout = $schemeTabLayout?->layout_json;

            $tabData[] = [
                'tab_code' => $tab->tab_code,
                'tab_name' => $tab->masterTab->tab_name ?? '',
                'tab_icon' => $tab->masterTab->tab_icon ?? '',
                'tab_short_name' => $tab->masterTab->tab_short_name ?? '',
                'fields'   => $fields,
                'layout'    => $layout,
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

    public static function store(int $schemeId, array $tabs): string
    {
        $dir = resource_path("views/schemes/scheme_{$schemeId}");

        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        foreach ($tabs as $tab) {

            $tabCode = $tab['tab_code'];

            /* ================= TAB 104 : DOCUMENT ================= */
            if ($tabCode == 104) {

                File::put(
                    "{$dir}/104.blade.php",
                    <<<BLADE
                {{-- DOCUMENT TAB --}}
                <livewire:enclosure-list :scheme_id="\$schemeId" :tabCode="$tabCode" />
                BLADE
                );
                continue;
            }

            /* ================= TAB 105 : SELF DECLARATION ================= */
           if ($tab['tab_code'] == 105) {

                $blade = "<div class=\"mt-4 space-y-3\">\n";

                foreach ($tab['fields'] as $field) {

                    $label = $field['level_name'] ?? 'Declaration';
                    $name  = $field['field_name'];
                    $value = $field['value'] ?? 1;
                    $blade .= <<<BLADE

                <div class="flex items-start gap-2">
                    <x-form.checkbox name="{$name}" value="{$value}" label="{$label}" wire:model="formData.{$name}"
                    />
                </div>
                BLADE;
                                }

                $blade .= "\n</div>";

                File::put(
                    $dir . "/105.blade.php",
                    $blade
                );

                continue;
            }


            /* ================= NORMAL FORM TABS ================= */

            // 🔥 load saved layout
            $layout = DB::table('scheme_tab_layouts')
                ->where('scheme_id', $schemeId)
                ->where('tab_code', $tabCode)
                ->value('layout_json');


            $layout = $layout ? json_decode($layout, true) : [];

            $fields = $tab['fields'] ?? [];
            $cursor = 0;
            $total  = count($fields);

            $blade = '';

            if (!empty($layout)) {

                foreach ($layout as $row) {

                    if ($cursor >= $total) break;

                    $cols = max(1, min(3, (int) $row['columns']));
                    $rowFields = array_slice($fields, $cursor, $cols);
                    $cursor += count($rowFields);

                    $blade .= "<div class=\"grid md:grid-cols-{$cols} gap-4 mt-4\">\n";

                    foreach ($rowFields as $field) {
                        $blade .= self::renderField($field);
                    }

                    $blade .= "</div>\n";
                }
            }

            /* ===== remaining fields fallback ===== */
            while ($cursor < $total) {
                $field = $fields[$cursor++];
                $blade .= "<div class=\"grid md:grid-cols-1 gap-4 mt-4\">\n";
                $blade .= self::renderField($field);
                $blade .= "</div>\n";
            }

            File::put("{$dir}/{$tabCode}.blade.php", $blade);
        }

        return $dir;
    }
    private static function renderField(array $field): string
    {
        $label = $field['level_name'] ?? '';
        $name  = $field['field_name'] ?? uniqid();
        $type  = $field['field_type'] ?? 'text';

        switch ($type) {

            case 'select':

                $optionsHtml = '';
                foreach ($field['options'] ?? [] as $opt) {
                    $opt = e($opt);
                    $optionsHtml .= "<option value=\"{$opt}\">{$opt}</option>\n";
                }

                return <<<BLADE
<x-form.select
    name="{$name}"
    label="{$label}"
    wire:model="formData.{$name}"
>
    <option value="">-- Select {$label} --</option>
    {$optionsHtml}
</x-form.select>
BLADE;

            case 'text':
            case 'number':
            case 'date':
                return <<<BLADE
<x-form.input
    type="{$type}"
    name="{$name}"
    label="{$label}"
    wire:model="formData.{$name}"
/>
BLADE;

            case 'textarea':
                return <<<BLADE
<x-form.textarea
    name="{$name}"
    label="{$label}"
    wire:model="formData.{$name}"
/>
BLADE;

            default:
                return "<!-- unsupported {$type} -->";
        }
    }
}
