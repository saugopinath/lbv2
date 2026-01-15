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

            /* =====================================================
         | DOCUMENT TAB (104) → x-component based (FIXED)
         ===================================================== */
            if ($tab['tab_code'] == 104) {

                $blade = <<<BLADE
{{-- ================= DOCUMENT UPLOAD TAB ================= --}}
@include('livewire.enclosure-list')
BLADE;

                File::put(
                    $dir . "/104.blade.php",
                    $blade
                );

                continue; // ⚠️ very important
            }

            /* =====================================================
         | DECLARATION TAB (105)
         ===================================================== */
            if ($tab['tab_code'] == 105) {

                $blade = "<div class=\"mt-4 space-y-3\">\n";

                foreach ($tab['fields'] as $field) {

                    $label = $field['level_name'] ?? 'Declaration';
                    $name  = 'declaration_' . ($field['id'] ?? uniqid());

                    $blade .= <<<BLADE
<div class="flex items-start gap-2">
    <x-form.checkbox
        name="{$name}"
        wire:model="formData.{$name}"
    />
    <span class="text-sm">{$label}</span>
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

            /* =====================================================
         | NORMAL FORM TABS (TEXT, DATE, SELECT etc.)
         ===================================================== */
            $blade = "<div class=\"grid md:grid-cols-2 gap-4 mt-4\">\n";

            foreach ($tab['fields'] as $field) {

                $label = $field['level_name'] ?? '';
                $name  = $field['field_label']
                    ?? $field['field_id']
                    ?? 'field_' . ($field['id'] ?? uniqid());

                $type  = $field['field_type'] ?? 'text';

                $blade .= "<div wire:key=\"field-{$name}\">\n";

                switch ($type) {

                    case 'text':
                    case 'date':
                    case 'number':
                    case 'password':
                        $blade .= <<<BLADE
<x-form.input
    type="{$type}"
    name="{$name}"
    wire:model="formData.{$name}"
    label="{$label}"
/>
BLADE;
                        break;

                    case 'textarea':
                        $blade .= <<<BLADE
<x-form.textarea
    name="{$name}"
    wire:model="formData.{$name}"
    label="{$label}"
/>
BLADE;
                        break;

                    case 'select':
                        $blade .= <<<BLADE
<x-form.select
    name="{$name}"
    wire:model.live="formData.{$name}"
    label="{$label}"
>
    <option value="">-- Select {$label} --</option>
BLADE;

                        foreach ($field['options'] ?? [] as $key => $opt) {
                            $blade .= "<option value=\"{$key}\">{$opt}</option>\n";
                        }

                        $blade .= "</x-form.select>\n";
                        break;

                    case 'checkbox':
                        $blade .= "<x-form.label name=\"{$label}\" />\n";
                        foreach ($field['options'] ?? [] as $opt) {
                            $blade .= <<<BLADE
<x-form.checkbox
    name="{$name}[]"
    value="{$opt}"
    wire:model="formData.{$name}"
    label="{$opt}"
/>
BLADE;
                        }
                        break;
                }

                $blade .= "</div>\n";
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
