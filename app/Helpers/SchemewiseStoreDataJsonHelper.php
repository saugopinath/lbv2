<?php

namespace App\Helpers;

use App\Models\SchemeAttachedDocMappings;
use App\Models\SchemeTabBasefield;
use App\Models\SchemeTabMapping;
use App\Models\SchemeTabFormField;
use App\Models\SectionLevelMaster;
use App\Models\SelfDeclerationBasefield;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\SchemeTabLayout;
use App\Models\AgeManagements;
use Illuminate\Support\Facades\Storage;

class SchemewiseStoreDataJsonHelper
{



    // public static function generateSchemeJson(int $schemeId): array
    // {
    //     // 🔹 Fetch age config once
    //     $ageConfig = AgeManagements::where('scheme_id', $schemeId)->first();

    //     $tabs = SchemeTabMapping::with('masterTab')
    //         ->where('scheme_id', $schemeId)
    //         ->where('is_active', true)
    //         ->orderBy('position')
    //         ->get();

    //     $tabData = [];

    //     foreach ($tabs as $tab) {

    //         $model = match ($tab->tab_code) {
    //             105 => SelfDeclerationBasefield::class,
    //             104 => SchemeAttachedDocMappings::class,
    //             default => SchemeTabFormField::class,
    //         };

    //         if ($tab->tab_code == 104) {

    //             $fields = $model::with('docType')
    //                 ->where('scheme_id', $schemeId)
    //                 ->where('tab_code', $tab->tab_code)
    //                 ->where('is_active', true)
    //                 ->orderBy('field_position')
    //                 ->get()
    //                 ->map(function ($field) {
    //                     $data = $field->toArray();
    //                     $data['doc_type_name'] = $field->docType?->name;
    //                     $data['doc_type_code'] = $field->docType?->code ?? null;
    //                     return $data;
    //                 })
    //                 ->toArray();
    //         } else {

    //             $fields = $model::where('scheme_id', $schemeId)
    //                 ->where('tab_code', $tab->tab_code)
    //                 ->where('is_active', true)
    //                 ->orderBy('field_position')
    //                 ->get()
    //                 ->map(function ($field) use ($ageConfig) {

    //                     $data = $field->toArray();

    //                     // 🔥 AGE FIELD ONLY
    //                     if ($data['field_name'] === 'age' && $ageConfig) {

    //                         $data['validation_rule'] = $ageConfig->getAgeValidationRule();
    //                         $data['age_limit']       = $ageConfig->getAgeLimit();
    //                         $data['is_special']      = $ageConfig->hasSpecialCase();
    //                     }

    //                     return $data;
    //                 })
    //                 ->toArray();
    //         }

    //         /** ================= LAYOUT ================= */

    //         $schemeTabLayout = SchemeTabLayout::where('scheme_id', $schemeId)
    //             ->where('tab_code', $tab->tab_code)
    //             ->first();

    //         $layout = $schemeTabLayout?->layout_json;

    //         $tabData[] = [
    //             'tab_code' => $tab->tab_code,
    //             'tab_name' => $tab->masterTab->tab_name ?? '',
    //             'tab_icon' => $tab->masterTab->tab_icon ?? '',
    //             'tab_short_name' => $tab->masterTab->tab_short_name ?? '',
    //             'fields' => $fields,
    //             'layout' => $layout,
    //         ];
    //     }

    //     return [
    //         'scheme_id' => $schemeId,
    //         'generated_at' => now()->toDateTimeString(),
    //         'tabs' => $tabData,
    //     ];
    // }

    // public static function storeSchemeJson(int $schemeId, array $data): string
    // {
    //     $path = "final_schemes_formdata/scheme_{$schemeId}.json";

    //     Storage::disk('local')->put(
    //         $path,
    //         json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    //     );

    //     return $path;
    // }


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
                105 => SelfDeclerationBasefield::class,
                104 => SchemeAttachedDocMappings::class,
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
                'fields' => $fields,
                'layout' => $layout,
            ];
        }
        return [
            'scheme_id' => $schemeId,
            'generated_at' => now()->toDateTimeString(),
            'tabs' => $tabData,
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
                <livewire:enclosure-list :scheme_id="\$schemeId" :tabCode="$tabCode" :application_id="\$applicationId" />
                BLADE
                );
                continue;
            }

            // if ($tabCode == 105) {

            //     // 🔹 fetch ordered self-declaration fields
            //     $fields = SelfDeclerationBasefield::where('scheme_id', $schemeId)
            //         ->where('tab_code', 105)
            //         ->where('is_active', true)
            //         ->orderBy('field_position')
            //         ->get()
            //         ->values();

            //     $sectionMap = SectionLevelMaster::pluck(
            //         'section_level_name',
            //         'id'
            //     )->toArray();

            //     $blade = "<div class='space-y-2'>";

            //     $lastSectionKey = null;

            //     foreach ($fields as $i => $field) {

            //         $hasSection = !empty($field->section_level_id);

            //         $currentKey = $hasSection
            //             ? $field->section_level_type . '-' . $field->section_level_id
            //             : null;

            //         $next = $fields[$i + 1] ?? null;

            //         $nextKey = (!empty($next?->section_level_id))
            //             ? $next->section_level_type . '-' . $next->section_level_id
            //             : null;

            //         /* ========= SECTION START ========= */
            //         if ($hasSection && $currentKey !== $lastSectionKey) {
            //             $title = $sectionMap[$field->section_level_id] ?? 'Section';

            //             $blade .= <<<BLADE
            //         <div class="mt-4 mb-2 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
            //             <span class="font-semibold text-indigo-700">
            //                 {$title}
            //             </span>
            //         </div>
            //         BLADE;
            //         }

            //         /* ========= FIELD ========= */
            //         $blade .= self::renderSelfDeclarationField($field);

            //         /* ========= SECTION END ========= */
            //         if ($hasSection && $currentKey !== $nextKey) {
            //             $blade .= "<div class='my-3'></div>";
            //         }

            //         if ($hasSection) {
            //             $lastSectionKey = $currentKey;
            //         }
            //     }

            //     $blade .= "</div>";

            //     File::put("{$dir}/105.blade.php", $blade);
            //     continue;
            // }

            /* ================= NORMAL FORM TABS ================= */
            if ($tabCode == 105) {

                $fields = SelfDeclerationBasefield::where('scheme_id', $schemeId)
                    ->where('tab_code', 105)
                    ->where('is_active', true)
                    ->orderBy('field_position')
                    ->get()
                    ->values();

                $sectionMap = SectionLevelMaster::pluck(
                    'section_level_name',
                    'id'
                )->toArray();

                $layout = DB::table('scheme_tab_layouts')
                    ->where('scheme_id', $schemeId)
                    ->where('tab_code', 105)
                    ->value('layout_json');

                $layout = $layout ? json_decode($layout, true) : [];

                // যদি layout wrapper থাকে
                if (isset($layout['layout'])) {
                    $layout = $layout['layout'];
                }

                $blade = "<div class='space-y-6'>";

                $cursor = 0;
                $total  = $fields->count();
                $layoutIndex = 0;

                $lastPrintedSection = null;

                while ($cursor < $total) {

                    $field = $fields[$cursor];

                    /* ===== Detect Section ===== */

                    $currentSectionKey = $field->section_level_id
                        ? $field->section_level_type . '-' . $field->section_level_id
                        : 'no_section';

                    /* ===== Print Section Header ===== */

                    if ($lastPrintedSection !== $currentSectionKey) {

                        if ($currentSectionKey !== 'no_section') {

                            [, $sectionId] = explode('-', $currentSectionKey);
                            $title = $sectionMap[$sectionId] ?? 'Section';

                            $blade .= <<<HTML
<div class="mt-6 mb-2 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
    <span class="font-semibold text-indigo-700">{$title}</span>
</div>
HTML;
                        }

                        $lastPrintedSection = $currentSectionKey;
                    }

                    /* ===== Get Layout Column ===== */

                    $requestedCols = $layout[$layoutIndex]['columns'] ?? 1;
                    $layoutIndex++;

                    // layout শেষ হয়ে গেলে repeat করাও যেতে পারে
                    if ($layoutIndex >= count($layout)) {
                        $layoutIndex = 0;
                    }

                    $rowFields = [];

                    /* ===== Build Row WITHOUT crossing section ===== */

                    while (
                        $cursor < $total &&
                        count($rowFields) < $requestedCols
                    ) {

                        $nextField = $fields[$cursor];

                        $nextSectionKey = $nextField->section_level_id
                            ? $nextField->section_level_type . '-' . $nextField->section_level_id
                            : 'no_section';

                        // 🔥 সবচেয়ে গুরুত্বপূর্ণ line
                        if ($nextSectionKey !== $currentSectionKey) {
                            break;
                        }

                        $rowFields[] = $nextField;
                        $cursor++;
                    }

                    $cols = max(1, count($rowFields));

                    $blade .= "<div class='grid grid-cols-1 md:grid-cols-{$cols} gap-5'>";

                    foreach ($rowFields as $rf) {
                        $blade .= self::renderSelfDeclarationField($rf);
                    }

                    $blade .= "</div>";
                }

                $blade .= "</div>";

                File::put("{$dir}/105.blade.php", $blade);
                continue;
            }
            // 🔥 load saved layout
            $layout = DB::table('scheme_tab_layouts')
                ->where('scheme_id', $schemeId)
                ->where('tab_code', $tabCode)
                ->value('layout_json');


            $layout = $layout ? json_decode($layout, true) : [];

            $fields = $tab['fields'] ?? [];
            $cursor = 0;
            $total = count($fields);

            $blade = '';

            if (!empty($layout)) {

                foreach ($layout as $row) {

                    if ($cursor >= $total)
                        break;

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
        $name = $field['field_name'] ?? uniqid();
        $type = $field['field_type'] ?? 'text';
        $minAttr = '';
        $maxAttr = '';
        if ($name === 'dob') {
            $minAttr = ':min="$minDOB"';
            $maxAttr = ':max="$maxDOB"';
        } elseif ($name === 'app_date' || $name === 'ds_date') {
            $minAttr = ':min="$minDate"';
            $maxAttr = ':max="$maxDate"';
        }
        $value = $field['value'] ?? 1;
        $placeholder = 'Enter ' . $field['level_name'] ?? '';

        /* ========= REQUIRED CONDITION (from validation_rule) ========= */
        $isRequired = false;

        if (!empty($field['validation_rule'])) {
            $rules = explode('|', $field['validation_rule']);
            $isRequired = in_array('required', $rules, true);
        }

        $requiredAttr = $isRequired ? 'required' : '';

        /* ========= READONLY CONDITION ========= */
        $isReadonly = !empty($field['is_readonly']) && (int) $field['is_readonly'] === 1;
        $readonlyAttr = $isReadonly ? 'readonly' : '';
        /* ========= wire:ignore condition ========= */
        $ignore = !empty($field['field_class']);
        $wireIgnore = $ignore ? 'wire:ignore' : '';

        /* ========= dependency condition ========= */
        $hasDependency =
            !empty($field['dependent_on']) &&
            !empty($field['dependent_on_values']) &&
            is_array($field['dependent_on_values']);

        $dependentOn = $field['dependent_on'] ?? null;
        $dependentValues = $field['dependent_on_values'] ?? [];

        /* ========= Alpine wrapper attrs (default empty) ========= */
        $xData = '';
        $xShow = '';
        $xCloak = '';

        if ($hasDependency) {

            $values = collect($dependentValues)
                ->map(fn($v) => "'" . (string) $v . "'")
                ->implode(',');

            $xData = <<<HTML
            x-data="{formData: @entangle('formData').live,visible: false,
                sync() {this.visible = [{$values}].includes(String(this.formData.{$dependentOn}));
                    if (!this.visible) {
                        this.formData.{$name} = null;
                    }
                },
                init() {
                    this.sync();
                    this.\$watch('formData.{$dependentOn}', () => this.sync());
                }
            }"
            HTML;

            $xShow = 'x-show="visible"';
            $xCloak = 'x-cloak';
        }

        /* ========= FIELD HTML (switch based) ========= */
        switch ($type) {

            case 'select':

                $optionsHtml = '';
                foreach (($field['options'] ?? []) as $key => $optionlabel) {
                    $key = e($key);
                    $optionlabel = e($optionlabel);
                    $optionsHtml .= "<option value=\"{$key}\">{$optionlabel}</option>\n";
                }

                $fieldHtml = <<<BLADE
                <x-form.select
                    name="{$name}"
                    label="{$label}"
                    data-wire="{$name}"
                    {$wireIgnore}
                     {$readonlyAttr}
                      {$requiredAttr}
                    wire:model.live="formData.{$name}"
                >
                    <option value="">-- Select {$label} --</option>
                    {$optionsHtml}
                </x-form.select>
                BLADE;
                break;

            case 'textarea':

                $fieldHtml = <<<BLADE
                <x-form.textarea
                    name="{$name}"
                    label="{$label}"
                    placeholder="{$placeholder}"
                    {$wireIgnore}
                     {$readonlyAttr}
                      {$requiredAttr}
                    wire:model.live="formData.{$name}"
                />
                BLADE;
                break;

            case 'checkbox':
                $fieldHtml = <<<BLADE
                    <x-form.checkbox
                        name="{$name}"
                        value="{$value}"
                        label="{$label}"
                        wire:model.live="formData.{$name}"
                    />

                BLADE;
                break;

            case 'text':
            case 'number':
            case 'date':
            default:

                $fieldHtml = <<<BLADE
                <x-form.input
                    type="{$type}"
                    name="{$name}"
                    label="{$label}"
                    placeholder="{$placeholder}"
                    {$wireIgnore}
                    {$readonlyAttr}
                    {$requiredAttr}
                    wire:model.live="formData.{$name}"
                    {$minAttr}
                {$maxAttr}
                />
                BLADE;
                break;
        }

        /* ========= FINAL OUTPUT ========= */
        return <<<BLADE
        <div {$xData} {$xShow} {$xCloak}>
            {$fieldHtml}
        </div>
        BLADE;
    }


    private static function renderSelfDeclarationField($field): string
    {
        $label = $field->level_name;
        $name = $field->field_name;
        $type = $field->field_type ?? 'text';
        $value = $field->value ?? 1;


        $paddingClass = $field->section_level_id ? 'pl-6' : 'pl-0';

        /* ========= OPTIONS NORMALIZE ========= */
        $options = [];

        if (!empty($field->options)) {
            if (is_string($field->options)) {
                $decoded = json_decode($field->options, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $options = $decoded;
                }
            } elseif (is_array($field->options)) {
                $options = $field->options;
            }
        }

        switch ($type) {

            /* ===== NUMBER ===== */
            case 'number':
                return <<<BLADE
                <div class="{$paddingClass}">
                    <x-form.input
                        type="number"
                        name="{$name}"
                        label="{$label}"
                        wire:model.live="formData.{$name}"
                    />
                </div>
                BLADE;

                /* ===== TEXTAREA ===== */
            case 'textarea':
                return <<<BLADE
                <div class="{$paddingClass}">
                    <x-form.textarea
                        name="{$name}"
                        label="{$label}"
                        wire:model.live="formData.{$name}"
                    />
                </div>
                BLADE;

                /* ===== SELECT ===== */
            case 'select':

                $optionsHtml = '';
                foreach ($options as $key => $text) {
                    if (is_int($key)) {
                        $key = $text;
                    }
                    $key = e($key);
                    $text = e($text);

                    $optionsHtml .= "<option value=\"{$key}\">{$text}</option>\n";
                }

                return <<<BLADE
                <div class="{$paddingClass}">
                    <x-form.select
                        name="{$name}"
                        label="{$label}"
                        wire:model.live="formData.{$name}"
                    >
                        <option value="">-- Select {$label} --</option>
                        {$optionsHtml}
                    </x-form.select>
                </div>
                BLADE;

                /* ===== RADIO ===== */
            case 'radio':

                $radioHtml = '';
                foreach ($options as $key => $text) {
                    if (is_int($key)) {
                        $key = $text;
                    }

                    $key = e($key);
                    $text = e($text);

                    $radioHtml .= <<<HTML
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            name="{$name}"
                            value="{$key}"
                            wire:model.live="formData.{$name}"
                        />
                        {$text}
                    </label>
                    HTML;
                }

                return <<<BLADE
                <div class="{$paddingClass}">
                    <label class="block font-medium text-gray-700 mb-1">{$label}</label>
                    <div class="flex flex-wrap gap-4">
                        {$radioHtml}
                    </div>
                </div>
                BLADE;

                /* ===== CHECKBOX ===== */
            case 'checkbox':
                return <<<BLADE
                <div class="{$paddingClass}">
                    <x-form.checkbox
                        name="{$name}"
                        label="{$label}"
                        value="{$value}"
                        wire:model.live="formData.{$name}"
                    />
                </div>
                BLADE;

                /* ===== DEFAULT TEXT ===== */
            default:
                return <<<BLADE
            <div class="{$paddingClass}">
                <x-form.input
                    type="text"
                    name="{$name}"
                    label="{$label}"
                    wire:model.live="formData.{$name}"
                />
            </div>
            BLADE;
        }
    }
}
