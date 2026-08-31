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

            if ($tab->tab_code == 102 && ($tab->is_current_address ?? false)) {
                $curFields = [];
                $existingNames = array_column($fields, 'field_name');
                foreach ($fields as $field) {
                    if (!empty($field['is_syncable']) && !str_starts_with($field['field_name'], 'cur_')) {
                        $name = $field['field_name'];
                        if (in_array('cur_' . $name, $existingNames)) continue;

                        $curField = $field;
                        $curField['field_name'] = 'cur_' . $name;

                        $curField['db_column'] = 'other_details';

                        $curField['level_name'] = 'Current ' . $field['level_name'];
                        if (!empty($curField['dependent_on'])) {
                            $curField['dependent_on'] = 'cur_' . $curField['dependent_on'];
                        }
                        $curField['is_syncable'] = false;
                        $curFields[] = $curField;
                    }
                }
                $fields = array_merge($fields, $curFields);
            }

            $tabData[] = [
                'tab_code' => $tab->tab_code,
                'tab_name' => $tab->masterTab->tab_name ?? '',
                'tab_icon' => $tab->masterTab->tab_icon ?? '',
                'tab_short_name' => $tab->masterTab->tab_short_name ?? '',
                'is_current_address' => $tab->is_current_address,
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
                <livewire:enclosure-list :scheme_id="\$schemeId" :tabCode="$tabCode" :application_id="\$applicationId" :form_preview="\$form_preview" />
                BLADE
                );
                continue;
            }

            /* ================= NORMAL FORM TABS ================= */
            if ($tabCode == 107) {
                // Farmer Custom Land Details dynamically generated from settings
                $modalPath = resource_path("views/components/dynamic-modal.blade.php");
                if (!File::exists($modalPath)) {
                    $modalContent = <<<'BLADE'
@props([
    'showVar' => 'showModal',
    'title' => 'Add Details',
    'fields' => [],
    'targetVar' => 'newLand',
    'submitAction' => 'addLand()',
])

<div x-show="{{ $showVar }}" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen w-full p-4 text-center">
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="{{ $showVar }} = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal Content -->
        <div class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-10">
            <div class="bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-bold">{{ $title }}</h3>
            </div>
            <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="space-y-4">
                    @foreach($fields as $field)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">{{ $field['label'] }}</label>
                            @if(($field['type'] ?? 'text') === 'select')
                                <select x-model="{{ $targetVar }}.{{ $field['name'] }}" class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">-- Select --</option>
                                    @foreach($field['options'] ?? [] as $val => $lbl)
                                        <option value="{{ $val }}">{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="{{ $field['type'] ?? 'text' }}" x-model="{{ $targetVar }}.{{ $field['name'] }}" class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="{{ $field['placeholder'] ?? '' }}" />
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2 border-t border-gray-200">
                <button type="button" @click="{{ $showVar }} = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 text-sm font-semibold">Cancel</button>
                <button type="button" @click="{{ $submitAction }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-semibold">Add</button>
            </div>
        </div>
    </div>
</div>
BLADE;
                    File::ensureDirectoryExists(dirname($modalPath));
                    File::put($modalPath, $modalContent);
                }

                $allFields = $tab['fields'] ?? [];
                $landsField = collect($allFields)->firstWhere('field_name', 'lands');

                if ($landsField) {
                    $tableSectionId = $landsField['section_level_id'] ?? null;
                    if (!$tableSectionId && !empty($allFields)) {
                        $tableSectionId = $allFields[0]['section_level_id'];
                    }

                    $tableFields = array_values(array_filter($allFields, function ($f) use ($tableSectionId) {
                        return $f['section_level_id'] == $tableSectionId && $f['field_name'] !== 'lands';
                    }));

                    $bottomFields = array_values(array_filter($allFields, function ($f) use ($tableSectionId) {
                        return $f['section_level_id'] != $tableSectionId;
                    }));

                    $newLandJsFields = [];
                    foreach ($tableFields as $f) {
                        $newLandJsFields[] = "{$f['field_name']}: ''";
                    }
                    $newLandJsStr = implode(', ', $newLandJsFields);

                    $validationJsFields = [];
                    foreach ($tableFields as $f) {
                        $rules = explode('|', $f['validation_rule'] ?? '');
                        if (in_array('required', $rules)) {
                            $validationJsFields[] = "!this.newLand.{$f['field_name']}";
                        }
                    }
                    if (empty($validationJsFields)) {
                        foreach ($tableFields as $f) {
                            $validationJsFields[] = "!this.newLand.{$f['field_name']}";
                        }
                    }
                    $validationJsStr = implode(' || ', $validationJsFields);

                    $tableHeadersHtml = "                        <th class=\"px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider\">Serial No</th>\n";
                    foreach ($tableFields as $f) {
                        $lbl = e($f['level_name'] ?? ucwords(str_replace('_', ' ', $f['field_name'])));
                        $tableHeadersHtml .= "                        <th class=\"px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider\">{$lbl}</th>\n";
                    }
                    $tableHeadersHtml .= "                        <th class=\"px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider\">Action</th>\n";

                    $tableCellsHtml = "                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\" x-text=\"index + 1\"></td>\n";
                    foreach ($tableFields as $f) {
                        $tableCellsHtml .= "                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-700\" x-text=\"land.{$f['field_name']}\"></td>\n";
                    }
                    $tableCellsHtml .= "                            <td class=\"px-6 py-4 whitespace-nowrap text-center text-sm font-medium\">
                                                    <button type=\"button\" @click=\"removeLand(index)\" class=\"text-red-600 hover:text-red-900 font-semibold\">Delete</button>
                                                </td>\n";

                    $bottomFieldsHtml = "<div class=\"grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 items-start\">\n";
                    foreach ($bottomFields as $f) {
                        $bottomFieldsHtml .= "    <div wire:key=\"field-norm-{$f['field_name']}\">\n";
                        $bottomFieldsHtml .= self::renderField($f);
                        $bottomFieldsHtml .= "    </div>\n";
                    }
                    $bottomFieldsHtml .= "</div>\n";

                    $hiddenInputsHtml = '';
                    foreach ($tableFields as $f) {
                        $hiddenInputsHtml .= "    <input type=\"hidden\" name=\"{$f['field_name']}\" id=\"{$f['field_name']}\" wire:model=\"formData.{$f['field_name']}\" />\n";
                    }

                    $modalFieldsArrayStr = '';
                    foreach ($tableFields as $f) {
                        $lbl = addslashes($f['level_name'] ?? ucwords(str_replace('_', ' ', $f['field_name'])));
                        $rules = explode('|', $f['validation_rule'] ?? '');
                        $isRequired = (in_array('required', $rules) || ($f['is_mendetory'] ?? 0) == 1) ? 'true' : 'false';
                        $optionsStr = '';
                        if (!empty($f['options']) && is_array($f['options'])) {
                            $opts = [];
                            foreach($f['options'] as $k => $v) {
                                $opts[] = "'" . addslashes($k) . "' => '" . addslashes($v) . "'";
                            }
                            $optionsStr = ", 'options' => [" . implode(", ", $opts) . "]";
                        }
                        $modalFieldsArrayStr .= "            ['name' => '{$f['field_name']}', 'label' => '{$lbl}', 'placeholder' => 'Enter {$lbl}', 'type' => '{$f['field_type']}', 'is_required' => {$isRequired}{$optionsStr}],\n";
                    }

                    $mapping = DB::table('scheme_tab_mappings')
                        ->where('scheme_id', $schemeId)
                        ->where('tab_code', $tabCode)
                        ->first();
                    $placement = $mapping->modal_placement ?? 'top-right';

                    $isTopRight = ($placement === 'top-right');
                    $isTopCenter = ($placement === 'top-center');
                    
                    $headerButtonHtml = '';
                    if ($isTopRight || $isTopCenter) {
                        $btnWrapperClass = $isTopCenter ? 'absolute left-1/2 -translate-x-1/2' : '';
                        $headerButtonHtml = <<<HTML
                                                <div class="{$btnWrapperClass}">
                                                    <button type="button" @click="showModal = true" class="flex items-center gap-2 bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold shadow hover:bg-blue-50 transition text-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        Add Land Details
                                                    </button>
                                                </div>
HTML;
                    }

                    $justifyClass = 'justify-end';
                    if (str_contains($placement, 'left')) $justifyClass = 'justify-start';
                    elseif (str_contains($placement, 'center')) $justifyClass = 'justify-center';

                    $barButtonHtml = <<<HTML
                                            <div class="px-6 py-4 flex {$justifyClass} items-center bg-gray-50 border-b border-gray-200">
                                                <button type="button" @click="showModal = true" class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold shadow hover:bg-blue-700 transition text-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Add Land Details
                                                </button>
                                            </div>
HTML;

                    $topButtonHtml = (str_starts_with($placement, 'top') && !$isTopRight && !$isTopCenter) ? $barButtonHtml : '';
                    $bottomButtonHtml = str_starts_with($placement, 'bottom') ? str_replace('border-b', 'border-t', $barButtonHtml) : '';

                    $blade = <<<BLADE
                                    <div x-data="{
                                        lands: @js(\$formData['lands'] ?? []),
                                        showModal: false,
                                        newLand: { {$newLandJsStr} },
                                        addLand() {
                                            if ({$validationJsStr}) {
                                                alert('Please fill all land detail fields.');
                                                return;
                                            }
                                            if (!this.lands) {
                                                this.lands = [];
                                            }
                                            this.lands.push({...this.newLand});
                                            this.newLand = { {$newLandJsStr} };
                                            this.showModal = false;
                                            \$wire.set('formData.lands', this.lands);
                                        },
                                        removeLand(index) {
                                            this.lands.splice(index, 1);
                                            \$wire.set('formData.lands', this.lands);
                                        }
                                    }" x-init="if(!this.lands || typeof this.lands !== 'object') { this.lands = []; }">

                                        <div class="mb-6 rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
                                            <!-- Header Bar -->
                                            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white relative">
                                                <h3 class="text-lg font-bold">Land Details</h3>
                                    {$headerButtonHtml}
                                            </div>
                                    {$topButtonHtml}

                                            <!-- Data Table -->
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                    {$tableHeadersHtml}                    </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        <template x-for="(land, index) in lands" :key="index">
                                                            <tr>
                                    {$tableCellsHtml}                        </tr>
                                                        </template>
                                                        <tr x-show="!lands || lands.length === 0">
                                                            <td colspan="10" class="px-6 py-8 text-center text-sm text-gray-500">
                                                                No land details added yet. Click "+ Add Land Details" to add a record.
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                    {$bottomButtonHtml}
                                        </div>

                                        <!-- Form Input Fields below table -->
                                    {$bottomFieldsHtml}
                                        <!-- Hidden Inputs to prevent JS type validation errors for fields not directly shown in the DOM -->
                                    {$hiddenInputsHtml}
                                        <!-- Add Land Details Modal (using reusable component) -->
                                        <x-dynamic-modal
                                            showVar="showModal"
                                            title="Add Land Details"
                                            targetVar="newLand"
                                            submitAction="addLand()"
                                            :fields="[
                                    {$modalFieldsArrayStr}        ]"
                                        />
                                    </div>
                                    BLADE;
                    File::put("{$dir}/107.blade.php", $blade);
                    continue;
                }
            } elseif ($tabCode == 108) {
                // Farmer Custom Family Details dynamically generated from settings
                $modalPath = resource_path("views/components/dynamic-modal.blade.php");
                if (!File::exists($modalPath)) {
                    $modalContent = <<<'BLADE'
@props([
    'showVar' => 'showModal',
    'title' => 'Add Details',
    'fields' => [],
    'targetVar' => 'newMember',
    'submitAction' => 'addMember()',
])

<div x-show="{{ $showVar }}" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen w-full p-4 text-center">
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="{{ $showVar }} = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal Content -->
        <div class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-10">
            <div class="bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-bold">{{ $title }}</h3>
            </div>
            <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="space-y-4">
                    @foreach($fields as $field)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">{{ $field['label'] }}</label>
                            @if(($field['type'] ?? 'text') === 'select')
                                <select x-model="{{ $targetVar }}.{{ $field['name'] }}" class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">-- Select --</option>
                                    @foreach($field['options'] ?? [] as $val => $lbl)
                                        <option value="{{ $val }}">{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="{{ $field['type'] ?? 'text' }}" x-model="{{ $targetVar }}.{{ $field['name'] }}" class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="{{ $field['placeholder'] ?? '' }}" />
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2 border-t border-gray-200">
                <button type="button" @click="{{ $showVar }} = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 text-sm font-semibold">Cancel</button>
                <button type="button" @click="{{ $submitAction }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-semibold">Add</button>
            </div>
        </div>
    </div>
</div>
BLADE;
                    File::ensureDirectoryExists(dirname($modalPath));
                    File::put($modalPath, $modalContent);
                }

                $allFields = $tab['fields'] ?? [];
                $familiesField = collect($allFields)->firstWhere('field_name', 'families');

                if ($familiesField) {
                    $tableSectionId = $familiesField['section_level_id'] ?? null;
                    if (!$tableSectionId && !empty($allFields)) {
                        $tableSectionId = $allFields[0]['section_level_id'];
                    }

                    $tableFields = array_values(array_filter($allFields, function ($f) use ($tableSectionId) {
                        return $f['section_level_id'] == $tableSectionId && $f['field_name'] !== 'families';
                    }));

                    $bottomFields = array_values(array_filter($allFields, function ($f) use ($tableSectionId) {
                        return $f['section_level_id'] != $tableSectionId;
                    }));

                    $newMemberJsFields = [];
                    foreach ($tableFields as $f) {
                        $newMemberJsFields[] = "{$f['field_name']}: ''";
                    }
                    $newMemberJsStr = implode(', ', $newMemberJsFields);

                    $validationJsFields = [];
                    foreach ($tableFields as $f) {
                        $rules = explode('|', $f['validation_rule'] ?? '');
                        if (in_array('required', $rules)) {
                            $validationJsFields[] = "!this.newMember.{$f['field_name']}";
                        }
                    }
                    if (empty($validationJsFields)) {
                        foreach ($tableFields as $f) {
                            $validationJsFields[] = "!this.newMember.{$f['field_name']}";
                        }
                    }
                    $validationJsStr = implode(' || ', $validationJsFields);

                    $tableHeadersHtml = "                        <th class=\"px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider\">Serial No</th>\n";
                    foreach ($tableFields as $f) {
                        $lbl = e($f['level_name'] ?? ucwords(str_replace('_', ' ', $f['field_name'])));
                        $tableHeadersHtml .= "                        <th class=\"px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider\">{$lbl}</th>\n";
                    }
                    $tableHeadersHtml .= "                        <th class=\"px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider\">Action</th>\n";

                    $tableCellsHtml = "                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-900\" x-text=\"index + 1\"></td>\n";
                    foreach ($tableFields as $f) {
                        $tableCellsHtml .= "                            <td class=\"px-6 py-4 whitespace-nowrap text-sm text-gray-700\" x-text=\"family.{$f['field_name']}\"></td>\n";
                    }
                    $tableCellsHtml .= "                            <td class=\"px-6 py-4 whitespace-nowrap text-center text-sm font-medium\">
                                                    <button type=\"button\" @click=\"removeMember(index)\" class=\"text-red-600 hover:text-red-900 font-semibold\">Delete</button>
                                                </td>\n";

                    $bottomFieldsHtml = "<div class=\"grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 items-start\">\n";
                    foreach ($bottomFields as $f) {
                        $bottomFieldsHtml .= "    <div wire:key=\"field-norm-{$f['field_name']}\">\n";
                        $bottomFieldsHtml .= self::renderField($f);
                        $bottomFieldsHtml .= "    </div>\n";
                    }
                    $bottomFieldsHtml .= "</div>\n";

                    $hiddenInputsHtml = '';
                    foreach ($tableFields as $f) {
                        $hiddenInputsHtml .= "    <input type=\"hidden\" name=\"{$f['field_name']}\" id=\"{$f['field_name']}\" wire:model=\"formData.{$f['field_name']}\" />\n";
                    }

                    $modalFieldsArrayStr = '';
                    foreach ($tableFields as $f) {
                        $lbl = addslashes($f['level_name'] ?? ucwords(str_replace('_', ' ', $f['field_name'])));
                        $rules = explode('|', $f['validation_rule'] ?? '');
                        $isRequired = (in_array('required', $rules) || ($f['is_mendetory'] ?? 0) == 1) ? 'true' : 'false';
                        $optionsStr = '';
                        if (!empty($f['options']) && is_array($f['options'])) {
                            $opts = [];
                            foreach($f['options'] as $k => $v) {
                                $opts[] = "'" . addslashes($k) . "' => '" . addslashes($v) . "'";
                            }
                            $optionsStr = ", 'options' => [" . implode(", ", $opts) . "]";
                        }
                        $modalFieldsArrayStr .= "            ['name' => '{$f['field_name']}', 'label' => '{$lbl}', 'placeholder' => 'Enter {$lbl}', 'type' => '{$f['field_type']}', 'is_required' => {$isRequired}{$optionsStr}],\n";
                    }

                    $mapping = DB::table('scheme_tab_mappings')
                        ->where('scheme_id', $schemeId)
                        ->where('tab_code', $tabCode)
                        ->first();
                    $placement = $mapping->modal_placement ?? 'top-right';

                    $isTopRight = ($placement === 'top-right');
                    $isTopCenter = ($placement === 'top-center');
                    
                    $headerButtonHtml = '';
                    if ($isTopRight || $isTopCenter) {
                        $btnWrapperClass = $isTopCenter ? 'absolute left-1/2 -translate-x-1/2' : '';
                        $headerButtonHtml = <<<HTML
                                                <div class="{$btnWrapperClass}">
                                                    <button type="button" @click="showModal = true" class="flex items-center gap-2 bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold shadow hover:bg-blue-50 transition text-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        Add Family Details
                                                    </button>
                                                </div>
HTML;
                    }

                    $justifyClass = 'justify-end';
                    if (str_contains($placement, 'left')) $justifyClass = 'justify-start';
                    elseif (str_contains($placement, 'center')) $justifyClass = 'justify-center';

                    $barButtonHtml = <<<HTML
                                            <div class="px-6 py-4 flex {$justifyClass} items-center bg-gray-50 border-b border-gray-200">
                                                <button type="button" @click="showModal = true" class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold shadow hover:bg-blue-700 transition text-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Add Family Details
                                                </button>
                                            </div>
HTML;

                    $topButtonHtml = (str_starts_with($placement, 'top') && !$isTopRight && !$isTopCenter) ? $barButtonHtml : '';
                    $bottomButtonHtml = str_starts_with($placement, 'bottom') ? str_replace('border-b', 'border-t', $barButtonHtml) : '';

                    $blade = <<<BLADE
                                    <div x-data="{
                                        families: @js(\$formData['families'] ?? []),
                                        showModal: false,
                                        newMember: { {$newMemberJsStr} },
                                        addMember() {
                                            if ({$validationJsStr}) {
                                                alert('Please fill all family detail fields.');
                                                return;
                                            }
                                            if (!this.families) {
                                                this.families = [];
                                            }
                                            this.families.push({...this.newMember});
                                            this.newMember = { {$newMemberJsStr} };
                                            this.showModal = false;
                                            \$wire.set('formData.families', this.families);
                                        },
                                        removeMember(index) {
                                            this.families.splice(index, 1);
                                            \$wire.set('formData.families', this.families);
                                        }
                                    }" x-init="if(!this.families || typeof this.families !== 'object') { this.families = []; }">

                                        <div class="mb-6 rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
                                            <!-- Header Bar -->
                                            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white relative">
                                                <h3 class="text-lg font-bold">Family Details</h3>
                                    {$headerButtonHtml}
                                            </div>
                                    {$topButtonHtml}

                                            <!-- Data Table -->
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200">
                                                    <thead class="bg-gray-50">
                                                        <tr>
                                    {$tableHeadersHtml}                    </tr>
                                                    </thead>
                                                    <tbody class="bg-white divide-y divide-gray-200">
                                                        <template x-for="(family, index) in families" :key="index">
                                                            <tr>
                                    {$tableCellsHtml}                        </tr>
                                                        </template>
                                                        <tr x-show="!families || families.length === 0">
                                                            <td colspan="10" class="px-6 py-8 text-center text-sm text-gray-500">
                                                                No family details added yet. Click "+ Add Family Details" to add a record.
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                    {$bottomButtonHtml}
                                        </div>

                                        <!-- Form Input Fields below table -->
                                    {$bottomFieldsHtml}
                                        <!-- Hidden Inputs to prevent JS type validation errors for fields not directly shown in the DOM -->
                                    {$hiddenInputsHtml}
                                        <!-- Add Family Details Modal (using reusable component) -->
                                        <x-dynamic-modal
                                            showVar="showModal"
                                            title="Add Family Details"
                                            targetVar="newMember"
                                            submitAction="addMember()"
                                            :fields="[
                                    {$modalFieldsArrayStr}        ]"
                                        />
                                    </div>
                                    BLADE;
                    File::put("{$dir}/108.blade.php", $blade);
                    continue;
                }
            } elseif ($tabCode == 105) {
                // Fully generic, configuration-driven Self Declaration generator
                $fields = SelfDeclerationBasefield::where('scheme_id', $schemeId)
                    ->where('tab_code', 105)
                    ->where('is_active', true)
                    ->orderBy('field_position')
                    ->get();

                $sections = SectionLevelMaster::where('scheme_id', $schemeId)
                    ->where('tab_code', 105)
                    ->where('is_active', true)
                    ->get()
                    ->sortBy(function ($sec) use ($fields) {
                        $firstField = $fields->where('section_level_id', $sec->id)->sortBy('field_position')->first();
                        return $firstField ? $firstField->field_position : 9999;
                    });

                $blade = "<div class=\"space-y-6\">\n";
                foreach ($sections as $sec) {
                    $secFields = $fields->where('section_level_id', $sec->id)->values();
                    if ($secFields->isEmpty()) continue;

                    if (!empty(trim($sec->section_level_name))) {
                        $blade .= "    <!-- {$sec->section_level_name} -->\n";
                        $blade .= "    <div class=\"mb-6\">\n";
                        $blade .= "        <div class=\"mt-6 mb-4 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded\">\n";
                        $blade .= "            <span class=\"font-bold text-indigo-700\">{$sec->section_level_name}</span>\n";
                        $blade .= "        </div>\n";
                    } else {
                        $blade .= "    <div class=\"mb-6\">\n";
                    }

                    if ($sec->section_level_short_name === 'aadhaar_sec') {
                        // Aadhaar Consent inline dropdown
                        $consentField = $secFields->first();
                        $optionsHtml = '<option value="">-- Select --</option>';
                        foreach ($consentField->options ?? [] as $val => $lbl) {
                            $optionsHtml .= "<option value=\"{$val}\">{$lbl}</option>";
                        }
                        
                        $blade .= <<<HTML
                                            <div class="flex items-center flex-wrap gap-2 text-gray-700 bg-gray-50 p-4 border border-gray-200 rounded">
                                                <span class="text-sm font-medium">I</span>
                                                <select name="{$consentField->field_name}" wire:model="formData.{$consentField->field_name}" class="inline-block py-1 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm font-semibold text-indigo-700">
                                                    {$optionsHtml}
                                                </select>
                                                <span class="text-sm font-medium">{$consentField->level_name}</span>
                                            </div>
                                            @error('formData.{$consentField->field_name}') <span class="text-red-500 text-xs mt-1 block">{{ \$message }}</span> @enderror
                                    HTML;
                    } elseif ($sec->section_level_short_name === 'pension_from_sec' || $sec->section_level_short_name === 'social_sec') {
                        // Checkboxes
                        $checkboxField = $secFields->first();
                        $checkboxesHtml = '';
                        foreach ($checkboxField->options ?? [] as $val => $lbl) {
                            $checkboxesHtml .= <<<HTML
                                                                <label class="flex items-center gap-2 cursor-pointer py-1">
                                                                    <input type="checkbox" name="{$checkboxField->field_name}[]" value="{$val}" wire:model="formData.{$checkboxField->field_name}" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                                                    <span class="text-sm text-gray-700 font-medium">{$lbl}</span>
                                                                </label>
                                                HTML;
                        }
                        
                        $levelNameHtml = '';
                        if (!empty(trim($checkboxField->level_name))) {
                            $levelNameHtml = "<div class=\"text-sm font-semibold text-gray-700 mb-2\">{$checkboxField->level_name}</div>";
                        }
                        
                        $blade .= <<<HTML
                                                        {$levelNameHtml}
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-gray-50 p-4 border border-gray-200 rounded">
                                                            {$checkboxesHtml}
                                                        </div>
                                                        @error('formData.{$checkboxField->field_name}') <span class="text-red-500 text-xs mt-1 block">{{ \$message }}</span> @enderror
                                                HTML;
                    } else {
                        // Fallback grid layout for generic sections
                        $cols = count($secFields);
                        $blade .= "        <div class=\"grid grid-cols-1 md:grid-cols-{$cols} gap-4\">\n";
                        foreach ($secFields as $f) {
                            $blade .= self::renderSelfDeclarationField($f);
                        }
                        $blade .= "        </div>\n";
                    }

                    $blade .= "    </div>\n";
                }

                // Fields with no section
                $noSectionFields = $fields->whereNull('section_level_id')->values();
                if (!$noSectionFields->isEmpty()) {
                    $blade .= "    <div class=\"mb-6\">\n";
                    $cols = min(3, count($noSectionFields));
                    $blade .= "        <div class=\"grid grid-cols-1 md:grid-cols-{$cols} gap-4\">\n";
                    foreach ($noSectionFields as $f) {
                        $blade .= self::renderSelfDeclarationField($f);
                    }
                    $blade .= "        </div>\n";
                    $blade .= "    </div>\n";
                }

                $blade .= "</div>\n";
                File::put("{$dir}/105.blade.php", $blade);
                continue;
            }
            $isCurrentAddress = ($tabCode == 102) && ($tab['is_current_address'] ?? false);
            $blade = '';
            $fields = $tab['fields'] ?? [];
            $renderFields = array_values(array_filter($fields, fn($f) => !str_starts_with($f['field_name'], 'cur_')));
            $syncableFields = array_values(array_filter($fields, fn($f) => !empty($f['is_syncable'])));
            $total = count($renderFields);
            $cursor = 0;

            if (!empty($layout)) {
                $blade .= "<div x-data=\"{ sameAsPermanent: false, formData: @entangle('formData').live, sync() { if(this.sameAsPermanent) { ";
                foreach ($syncableFields as $field) {
                    $name = $field['field_name'];
                    $blade .= "this.formData.cur_{$name} = this.formData.{$name}; ";
                }
                $blade .= "this.\$nextTick(() => { setTimeout(() => { document.querySelectorAll('[name^=\\'cur_\\']').forEach(el => delete el.dataset.loaded); if(typeof window.initMasterData === 'function') window.initMasterData(); }, 100); }); ";
                $blade .= " } } }\" x-init=\"\$watch('sameAsPermanent', v => sync()); ";
                foreach ($syncableFields as $field) {
                    $name = $field['field_name'];
                    $blade .= "\$watch('formData.{$name}', v => { if(sameAsPermanent) sync(); }); ";
                }
                $blade .= "\">\n";

                foreach ($layout as $row) {
                    if ($cursor >= $total)
                        break;
                    $cols = max(1, min(3, (int) $row['columns']));
                    $rowFields = array_slice($renderFields, $cursor, $cols);
                    $cursor += count($rowFields);
                    $blade .= "<div class=\"grid md:grid-cols-{$cols} gap-4 mt-4\">\n";
                    foreach ($rowFields as $field) {
                        $blade .= self::renderField($field);
                    }
                    $blade .= "</div>\n";
                }

                if ($cursor < $total) {
                    while ($cursor < $total) {
                        $field = $renderFields[$cursor++];
                        $blade .= "<div class=\"grid md:grid-cols-1 gap-4 mt-4\">\n";
                        $blade .= self::renderField($field);
                        $blade .= "</div>\n";
                    }
                }

                if ($isCurrentAddress) {
                    $blade .= <<<HTML
                    <div class="mt-8 mb-4 p-4 bg-gray-50 border-y border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Current Address</h3>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="sameAsPermanent" class="w-4 h-4 text-indigo-600 rounded">
                            <span class="text-sm font-medium text-gray-700">Same as Permanent Address</span>
                        </label>
                    </div>
                    HTML;


                    // Filter fields that are marked as syncable
                    $totalSync = count($syncableFields);

                    $cursor = 0;
                    foreach ($layout as $row) {
                        if ($cursor >= $totalSync)
                            break;
                        $cols = max(1, min(3, (int) $row['columns']));
                        $rowFields = array_slice($syncableFields, $cursor, $cols);
                        $cursor += count($rowFields);
                        $blade .= "<div class=\"grid md:grid-cols-{$cols} gap-4 mt-4\">\n";
                        foreach ($rowFields as $field) {
                            $curName = 'cur_' . $field['field_name'];
                            $curField = collect($fields)->firstWhere('field_name', $curName);
                            if ($curField) {
                                $curField['is_readonly'] = 'sameAsPermanent';
                                $blade .= self::renderField($curField);
                            } else {
                                $curField = $field;
                                $curField['field_name'] = $curName;
                                $curField['db_column'] = 'other_details';
                                $curField['is_readonly'] = 'sameAsPermanent';
                                $blade .= self::renderField($curField);
                            }
                        }
                        $blade .= "</div>\n";
                    }
                    if ($cursor < $totalSync) {
                        while ($cursor < $totalSync) {
                            $field = $syncableFields[$cursor++];
                            $blade .= "<div class=\"grid md:grid-cols-1 gap-4 mt-4\">\n";
                            $curName = 'cur_' . $field['field_name'];
                            $curField = collect($fields)->firstWhere('field_name', $curName);
                            if ($curField) {
                                $curField['is_readonly'] = 'sameAsPermanent';
                                $blade .= self::renderField($curField);
                            } else {
                                $curField = $field;
                                $curField['field_name'] = $curName;
                                $curField['db_column'] = 'other_details';
                                $curField['is_readonly'] = 'sameAsPermanent';
                                $blade .= self::renderField($curField);
                            }
                            $blade .= "</div>\n";
                        }
                    }
                }
                $blade .= "</div>\n";
            } else {
                // No layout saved, default grid
                $blade .= "<div x-data=\"{ sameAsPermanent: false, formData: @entangle('formData').live, sync() { if(this.sameAsPermanent) { ";
                foreach ($syncableFields as $field) {
                    $name = $field['field_name'];
                    $blade .= "this.formData.cur_{$name} = this.formData.{$name}; ";
                }
                $blade .= "this.\$nextTick(() => { setTimeout(() => { document.querySelectorAll('[name^=\\'cur_\\']').forEach(el => delete el.dataset.loaded); if(typeof window.initMasterData === 'function') window.initMasterData(); }, 100); }); ";
                $blade .= " } } }\" x-init=\"\$watch('sameAsPermanent', v => sync()); ";
                foreach ($syncableFields as $field) {
                    $name = $field['field_name'];
                    $blade .= "\$watch('formData.{$name}', v => { if(sameAsPermanent) sync(); }); ";
                }
                $blade .= "\">\n";

                $blade .= "<div class=\"grid md:grid-cols-2 gap-4 mt-4\">\n";
                foreach ($renderFields as $field) {
                    $blade .= self::renderField($field);
                }
                $blade .= "</div>\n";

                if ($isCurrentAddress) {
                    $blade .= <<<HTML
                    <div class="mt-8 mb-4 p-4 bg-gray-50 border-y border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Current Address</h3>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="sameAsPermanent" class="w-4 h-4 text-indigo-600 rounded">
                            <span class="text-sm font-medium text-gray-700">Same as Permanent Address</span>
                        </label>
                    </div>
                    HTML;
                    $blade .= "<div class=\"grid md:grid-cols-2 gap-4 mt-4\">\n";
                    foreach ($syncableFields as $field) {
                        $curName = 'cur_' . $field['field_name'];
                        $curField = collect($fields)->firstWhere('field_name', $curName);
                        if ($curField) {
                            $curField['is_readonly'] = 'sameAsPermanent';
                            $blade .= self::renderField($curField);
                        } else {
                            $curField = $field;
                            $curField['field_name'] = $curName;
                            $curField['db_column'] = 'other_details';
                            $curField['is_readonly'] = 'sameAsPermanent';
                            $blade .= self::renderField($curField);
                        }
                    }
                    $blade .= "</div>\n";
                }
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
        $validation = $field['validation_rule'] ?? ''; // <--- নতুন
        $regex = $field['regex'] ?? null;              // <--- নতুন
        $dynamicAttr = self::generateDynamicInputLogic($name, $validation, $regex);
        if ($name === 'ifscode' || $name === 'dob') {
            $wireModelMode = 'wire:model.blur';
        } else {
            $wireModelMode = 'wire:model';
        }
        // if ($name === 'ifscode' || $name === 'ifsc_code') {
        //     $wireModelMode = 'wire:model.live';
        // }
        // বাকি যেসব ফিল্ডে digits বা size আছে সেগুলোতে .blur হবে
        // elseif (str_contains($validation, 'digits') || str_contains($validation, 'size')) {
        //     $wireModelMode = 'wire:model.blur';
        // }
        // // অন্য সব সাধারণ ফিল্ডে .live থাকবে
        // else {
        //     $wireModelMode = 'wire:model.live';
        // }
        $isConfirmField = false;
        $isEdit = false;
        if (!empty($field['validation_rule'])) {
            $rules = explode('|', $field['validation_rule']);
            $isRequired = in_array('required', $rules, true);

            foreach ($rules as $rule) {
                if (str_starts_with($rule, 'same:')) {
                    $isConfirmField = true;
                    break;
                }
            }
        }

        if ($isConfirmField) {
            $type = 'password';
        }

        // $type = $field['field_type'] ?? 'text';
        $value = $field['value'] ?? 1;
        $placeholder = 'Enter ' . $field['level_name'] ?? '';
        $isRequired = false;
        if (!empty($field['validation_rule'])) {
            $rules = explode('|', $field['validation_rule']);
            $isRequired = in_array('required', $rules, true);
        }

        $requiredAttr = $isRequired ? 'required' : '';
        $isReadonly = !empty($field['is_readonly']);
        $readonlyVal = $field['is_readonly'] ?? 0;

        if (is_string($readonlyVal) && $readonlyVal === 'sameAsPermanent') {
            $readonlyAttr = '::readonly="sameAsPermanent" ::disabled="sameAsPermanent"';
        } else {
            $readonlyAttr = ((int)$readonlyVal === 1) ? 'readonly' : '';
        }
        $disabledAttr = in_array($name, ['ds_registration_no', 'application_type', 'ds_date']) ? ':disabled="$isEdit"' : '';
        $ignore = !empty($field['field_class']);
        $wireIgnore = $ignore ? 'wire:ignore' : '';
        $hasDependency =
            !empty($field['dependent_on']) &&
            !empty($field['dependent_on_values']) &&
            is_array($field['dependent_on_values']);

        $dependentOn = $field['dependent_on'] ?? null;
        $dependentValues = $field['dependent_on_values'] ?? [];
        $minAttr = '';
        $maxAttr = '';
        if ($name === 'dob') {
            $minAttr = ':min="$minDOB"';
            $maxAttr = ':max="$maxDOB"';
        } elseif ($name === 'application_date' || $name === 'ds_date') {
            $minAttr = ':min="$minDate"';
            $maxAttr = ':max="$maxDate"';
        }

        $xData = '';
        $xShow = '';
        $xCloak = '';

        if ($hasDependency) {
            $values = collect($dependentValues)
                ->map(fn($v) => "'" . (string) $v . "'")
                ->implode(',');
            $xData = <<<HTML
        x-data="{
            formData: @entangle('formData').live,
            get isVisible() {
                if (!this.formData) return false;
                return [{$values}].includes(String(this.formData.{$dependentOn}));
            },
            sync() {
                if (!this.isVisible && this.formData.hasOwnProperty('{$name}')) {
                    this.formData.{$name} = null;
                }
            },
            init() {
                this.sync();
                this.\$watch('formData.{$dependentOn}', () => this.sync());
            }
        }"
        HTML;
            $xShow = 'x-show="isVisible"';
            $xCloak = 'x-cloak';
            $wireKey = 'wire:key="field-dep-' . $name . '"';
        } else {
            $wireKey = 'wire:key="field-norm-' . $name . '"';
        }

        switch ($type) {
            case 'select':
                // ✅ SPECIAL RENDER FOR app_type ONLY
                if ($name === 'application_type') {

                    $fieldHtml = <<<BLADE
        <div wire:key="field-norm-application_type">
            <x-form.select
                name="application_type"
                label="Application Type"
                data-wire="application_type"
                required
                wire:model.blur="formData.application_type"
                {$disabledAttr}
            >
                <option value="">-- Select Application Type --</option>

                @foreach(\$appTypeOptions as \$value => \$label)
                    <option value="{{ \$value }}">{{ \$label }}</option>
                @endforeach

            </x-form.select>
        </div>
        BLADE;

                    break;
                }

                // 🔹 NORMAL SELECT FOR OTHER FIELDS
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
                    {$disabledAttr}
                    wire:model="formData.{$name}"
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
                      {$disabledAttr}
                    wire:model="formData.{$name}"
                />
                BLADE;
                break;
            case 'checkbox':
                $fieldHtml = <<<BLADE
                    <x-form.checkbox
                        name="{$name}"
                        value="{$value}"
                        label="{$label}"
                        {$disabledAttr}
                        wire:model="formData.{$name}"
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
                    {$minAttr}
                    {$maxAttr}
                    {$disabledAttr}
                    {$wireModelMode}="formData.{$name}"
                    {$dynamicAttr}
                />
                BLADE;
                break;
        }

        /* ========= FINAL OUTPUT ========= */
        return <<<BLADE
        <div {$xData} {$xShow} {$xCloak} {$wireKey}>
            {$fieldHtml}
        </div>
        BLADE;
    }


    private static function renderSelfDeclarationField($field): string
    {
        $label = $field->level_name;
        $name = $field->field_name;
        $type = $field->field_type ?? 'text';
        $validation = $field->validation_rule ?? ''; // <--- নতুন
        $regex = $field->regex ?? null;
        $value = $field->value ?? 1;
        $placeholder = 'Enter ' . $field->level_name ?? '';
        $paddingClass = $field->section_level_id ? 'pl-6' : 'pl-0';
        $options = [];
        $dynamicAttr = self::generateDynamicInputLogic($name, $validation, $regex);
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
                        placeholder="{$placeholder}"
                        wire:model="formData.{$name}"
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
                        placeholder="{$placeholder}"
                        wire:model="formData.{$name}"
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
                        wire:model="formData.{$name}"
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
                            wire:model="formData.{$name}"
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
                        wire:model="formData.{$name}"
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
                    placeholder="{$placeholder}"
                    wire:model="formData.{$name}"
                    {$dynamicAttr}
                />
            </div>
            BLADE;
        }
    }

    private static function generateDynamicInputLogic(string $name, ?string $validation, ?string $regex): string
    {
        if (empty($validation) && empty($regex)) return '';

        $maxDigits = null;
        // validation_rule থেকে size:11 বা digits:10 বের করা
        if ($validation && preg_match('/(?:digits|size):(\d+)/', $validation, $matches)) {
            $maxDigits = $matches[1];
        }

        $cleanInput = "";

        // IFSC Code এর জন্য বিশেষ লজিক (অক্ষর + সংখ্যা এবং Uppercase)
        if ($name === 'ifscode' || $name === 'ifsc_code') {
            $cleanInput = "\$el.value = \$el.value.toUpperCase().replace(/[^A-Z0-9]/g, '')";
        }
        // সংখ্যা ফিল্টার (Mobile/Pin/Bank Acc) - যেখানে digits বা regex এ শুধু সংখ্যা আছে
        elseif (($validation && str_contains($validation, 'digits')) || ($regex && str_contains($regex, '0-9') && !str_contains($regex, 'A-Z'))) {
            $cleanInput = "\$el.value = \$el.value.replace(/[^0-9]/g, '')";
        }
        // নাম ফিল্টার (Letters only)
        elseif ($regex && $regex === '^[A-Za-z .]+$') {
            $cleanInput = "\$el.value = \$el.value.replace(/[^A-Za-z .]/g, '')";
        }

        if (empty($cleanInput)) return '';

        $sliceCode = $maxDigits ? ".slice(0, {$maxDigits})" : "";

        return "x-on:input.stop=\"{$cleanInput}{$sliceCode}; \$wire.set('formData.{$name}', \$el.value, false)\"";
    }
}
