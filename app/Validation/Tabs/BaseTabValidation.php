<?php

namespace App\Validation\Tabs;

use App\Models\AgeManagements;
use Illuminate\Support\Facades\File;

/**
 * Abstract Base Class: The foundation for all tab validation logic across all schemes.
 * 
 * An 'abstract' class cannot be instantiated directly using 'new BaseTabValidation()'.
 * It serves as a blueprint containing shared helper functions, JSON parsing logic, 
 * and database configuration adjustments that child classes inherit.
 */
abstract class BaseTabValidation
{
    protected string $schemeId;
    protected string $tabCode;

    /**
     * Constructor: Automatically receives and assigns the active scheme & tab IDs.
     */
    public function __construct(string $schemeId, string $tabCode)
    {
        $this->schemeId = $schemeId;
        $this->tabCode = $tabCode;
    }

    /**
     * Common Core Method: Reads the scheme's JSON configuration file from storage
     * and compiles default Laravel validation rules for fields inside the current tab.
     */
    protected function getJsonRules(): array
    {
        // 1. Locate the JSON schema file for the current scheme
        $path = storage_path("app/final_schemes_formdata/scheme_{$this->schemeId}.json");
        if (!File::exists($path)) {
            return [];
        }

        $json = json_decode(File::get($path), true) ?? [];
        $rules = [];

        // 2. Query dynamic age limits configured in the database for this scheme
        $ageConfig = AgeManagements::where('scheme_id', $this->schemeId)->first();

        // 3. Loop through tabs in JSON to match the active tab code
        foreach ($json['tabs'] ?? [] as $tab) {
            if ((string) ($tab['tab_code'] ?? '') !== (string) $this->tabCode) {
                continue;
            }

            // 4. Loop through fields in the tab and parse their raw validation pipe string (e.g. "required|string")
            foreach ($tab['fields'] ?? [] as $field) {
                $fieldName = $field['field_name'];
                $fieldRules = explode('|', $field['validation_rule'] ?? '');

                // Convert 'required' rule to 'accepted' for HTML checkboxes
                if (($field['field_type'] ?? '') === 'checkbox') {
                    $fieldRules = array_map(fn($r) => $r === 'required' ? 'accepted' : $r, $fieldRules);
                }

                // Inject dynamic database age constraints for 'age' fields
                if ($fieldName === 'age' && $ageConfig) {
                    $fieldRules = $this->applyAgeRules($fieldRules, $ageConfig);
                }

                // Inject dynamic database date boundaries for 'dob' (Date of Birth) fields
                if ($fieldName === 'dob' && $ageConfig) {
                    $fieldRules = $this->applyDobRules($fieldRules, $ageConfig);
                }

                // Format key to match Livewire's form array binding (formData.field_name)
                $rules["formData.{$fieldName}"] = array_values(array_filter($fieldRules));
            }
        }

        return $rules;
    }

    /**
     * Helper Method: Replaces fixed JSON age limits with dynamic DB values from AgeManagements.
     */
    protected function applyAgeRules(array $fieldRules, $ageConfig): array
    {
        // Remove existing static min/max/integer rules from the raw array
        $fieldRules = array_filter($fieldRules, function ($rule) {
            $r = trim($rule);
            return !str_starts_with($r, 'min:') &&
                !str_starts_with($r, 'max:') &&
                $r !== 'integer' &&
                $r !== 'numeric';
        });

        // Re-inject dynamic values from DB
        $fieldRules[] = 'integer';
        if (!is_null($ageConfig->min_age)) {
            $fieldRules[] = "min:{$ageConfig->min_age}";
        }
        if (!is_null($ageConfig->max_age)) {
            $fieldRules[] = "max:{$ageConfig->max_age}";
        }

        return $fieldRules;
    }

    /**
     * Helper Method: Calculates relative valid birth dates based on min/max age rules.
     */
    protected function applyDobRules(array $fieldRules, $ageConfig): array
    {
        // Remove static after/before date rules
        $fieldRules = array_filter($fieldRules, function ($rule) {
            $r = trim($rule);
            return !str_starts_with($r, 'after_or_equal:') &&
                !str_starts_with($r, 'before_or_equal:');
        });

        // Calculate dynamic past dates based on max and min age limits
        if (!is_null($ageConfig->max_age)) {
            $minDate = now()->subYears($ageConfig->max_age)->format('Y-m-d');
            $fieldRules[] = "after_or_equal:{$minDate}";
        }
        if (!is_null($ageConfig->min_age)) {
            $maxDate = now()->subYears($ageConfig->min_age)->format('Y-m-d');
            $fieldRules[] = "before_or_equal:{$maxDate}";
        }

        return $fieldRules;
    }

    /**
     * Locked Final Method: Sanitizes form inputs across ALL schemes.
     * The 'final' keyword ensures child scheme files CANNOT override or skip this security method.
     */
    public final function sanitizeFormData(array $formData): array
    {
        return array_map(fn($val) => is_string($val) ? trim($val) : $val, $formData);
    }

    /**
     * Contract Method: Enforces every extending class to implement its own getRules() method.
     * If a child class forgets to define getRules(), PHP will raise a compilation error.
     */
    abstract public function getRules(): array;
}
