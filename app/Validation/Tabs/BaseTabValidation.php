<?php

namespace App\Validation\Tabs;

use App\Models\AgeManagements;
use Illuminate\Support\Facades\File;

/**
 * Class BaseTabValidation
 * 
 * Architectural Purpose:
 * Provides an abstract blueprint for multi-tab validation across different scheme forms.
 * It encapsulates JSON parsing, dynamic database rule merging (e.g., dynamic age limits),
 * date-of-birth threshold calculations, and sanitization routines.
 * 
 * Design Principles Followed:
 * - DRY: Common array conversions, string cleanup, and rule building live in one place.
 * - SOLID (Open/Closed): Concrete classes extend this to add custom rules without modifying core schema parsing.
 */
abstract class BaseTabValidation
{
    /** @var string The active scheme identifier (e.g., 'SCHEME_01') */
    protected string $schemeId;

    /** @var string The active tab code identifier (e.g., '101' or '104') */
    protected string $tabCode;

    /**
     * Class Constructor
     * 
     * @param string $schemeId The scheme context currently being validated.
     * @param string $tabCode   The specific form tab code being evaluated.
     */
    public function __construct(string $schemeId, string $tabCode)
    {
        $this->schemeId = $schemeId;
        $this->tabCode = $tabCode;
    }

    /**
     * Main Core Pipeline: Reads the scheme JSON schema file, locates fields matching
     * the target tab code, extracts validation strings, applies field-specific 
     * transformations, and maps them to Livewire-compatible 'formData.*' array rules.
     * 
     * @return array Matrix of Livewire form rules (e.g. ['formData.age' => ['required', 'min:18']])
     */
    protected function parseJsonSchemaRules(): array
    {
        // 1. Load raw JSON schema array from disk storage
        $schema = $this->loadSchemeJsonSchema();
        if (empty($schema)) {
            return [];
        }

        // 2. Query dynamic age boundaries configured in the database for this scheme
        $ageConfig = $this->getAgeManagementConfig();
        $rules = [];

        // 3. Iterate through all tabs in the schema to locate the matching tab
        foreach ($schema['tabs'] ?? [] as $tab) {
            // Skip tabs that do not match the current tabCode context
            if ((string) ($tab['tab_code'] ?? '') !== (string) $this->tabCode) {
                continue;
            }

            // 4. Loop through every input field definition declared inside the active tab
            foreach ($tab['fields'] ?? [] as $field) {
                // Safely extract field key name across standard and enclosure schemas
                $fieldName = $this->resolveFieldName($field);
                if (!$fieldName) {
                    continue;
                }

                // 5. Select rule extraction strategy based on tab type (File Enclosures vs Standard Form Fields)
                $fieldRules = $this->isEnclosureTab($tab)
                    ? $this->getEnclosingRules($field)
                    : explode('|', $field['validation_rule'] ?? '');

                // 6. Inject dynamic modifications (e.g., checkboxes, dynamic age limits, DOB dates)
                $fieldRules = $this->applyFieldSpecificTransformations($field, $fieldName, $fieldRules, $ageConfig);

                // 7. Format into Livewire array binding syntax and clean array keys
                // Extract human-readable level_name / display label with fallback
                $levelName = $field['level_name']
                    ?? $field['label_name']
                    ?? $field['label']
                    ?? $field['doc_type']['name']
                    ?? null;

                // Return structured payload carrying rules and display label
                $rules["formData.{$fieldName}"] = [
                    'level_name' => $levelName ?: ucwords(str_replace('_', ' ', $fieldName)),
                    'rules'      => implode('|', array_values(array_filter($fieldRules))),
                ];
            }
        }

        return $rules;
    }

    /**
     * Converts structured schema rules into pure Laravel-compatible rules array.
     * Call this when passing rules to Livewire's $this->validate().
     * 
     * @return array ['formData.age' => 'required|integer']
     */
    public function getLaravelRules(): array
    {
        $parsed = $this->getRules();
        $laravelRules = [];

        foreach ($parsed as $key => $config) {
            if (is_array($config) && isset($config['rules'])) {
                $laravelRules[$key] = $config['rules'];
            } else {
                $laravelRules[$key] = $config;
            }
        }

        return $laravelRules;
    }

    /**
     * Identification Helper: Determines if a tab represents a file upload/enclosure section.
     * Checks for explicit tab code (104) or a tab type set to 'enclosure'.
     * 
     * @param array $tab Tab definition array from JSON
     * @return bool True if the tab is an enclosure upload section
     */
    protected function isEnclosureTab(array $tab): bool
    {
        return ((int) ($tab['tab_code'] ?? 0) === 104) || (($tab['type'] ?? '') === 'enclosure');
    }

    /**
     * Name Resolution Helper: Normalizes how field names are derived.
     * Checks direct field_name first, falls back to doc_type short names for dynamic document uploads.
     * 
     * @param array $field Field definition array from JSON
     * @return string|null Resolved string name or null if invalid
     */
    protected function resolveFieldName(array $field): ?string
    {
        return $field['field_name']
            ?? $field['doc_type']['short_name']
            ?? (!empty($field['doc_type_code']) ? "doc_{$field['doc_type_code']}" : null);
    }

    /**
     * Field Rule Transformer: Inspects field types and names to apply dynamic framework requirements.
     * - Converts 'required' to 'accepted' for HTML checkboxes.
     * - Dynamically applies dynamic database age limits to Age and DOB fields.
     * 
     * @param array $field Raw field definition from JSON
     * @param string $fieldName Resolved field name
     * @param array $fieldRules Standard rule array parsed from string
     * @param object|null $ageConfig Eloquent model containing dynamic min/max age rules
     * @return array Transformed rule array
     */
    protected function applyFieldSpecificTransformations(array $field, string $fieldName, array $fieldRules, ?object $ageConfig): array
    {
        // Checkboxes require Laravel's 'accepted' rule rather than 'required' to handle boolean values
        if (($field['field_type'] ?? '') === 'checkbox') {
            $fieldRules = array_map(fn($r) => $r === 'required' ? 'accepted' : $r, $fieldRules);
        }

        // Apply dynamic database age/DOB validations if database config exists
        if ($ageConfig) {
            // Target direct age input fields (excluding DOB)
            if (str_contains($fieldName, 'age') && !str_contains($fieldName, 'dob')) {
                $fieldRules = $this->applyAgeRules($fieldRules, $ageConfig);
            }

            // Target Date of Birth (DOB) date fields
            if (str_contains($fieldName, 'dob') || str_contains($fieldName, 'date_of_birth')) {
                $fieldRules = $this->applyDobRules($fieldRules, $ageConfig);
            }
        }

        return $fieldRules;
    }

    /**
     * Age Rule Mutator: Strips static JSON min/max rules and replaces them with 
     * dynamic dynamic constraints fetched from the database.
     * 
     * @param array $fieldRules Current field rule array
     * @param object $ageConfig DB configuration object containing min_age/max_age
     * @return array Updated rules with dynamic min/max constraints
     */
    protected function applyAgeRules(array $fieldRules, object $ageConfig): array
    {
        // Remove static integer, min, and max rules present in JSON definition
        $fieldRules = array_filter($fieldRules, function ($rule) {
            $r = trim($rule);
            return !str_starts_with($r, 'min:') &&
                !str_starts_with($r, 'max:') &&
                $r !== 'integer' &&
                $r !== 'numeric';
        });

        // Re-inject validated type and dynamic DB thresholds
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
     * DOB Date Calculation Mutator: Computes valid past calendar date boundaries 
     * based on active dynamic min/max age requirements.
     * 
     * @param array $fieldRules Current field rule array
     * @param object $ageConfig DB configuration object containing min_age/max_age
     * @return array Updated rules with relative 'after_or_equal' and 'before_or_equal' date strings
     */
    protected function applyDobRules(array $fieldRules, object $ageConfig): array
    {
        // Remove hardcoded static date range constraints
        $fieldRules = array_filter($fieldRules, function ($rule) {
            $r = trim($rule);
            return !str_starts_with($r, 'after_or_equal:') &&
                !str_starts_with($r, 'before_or_equal:');
        });

        // Use safe isolated datetime instance to avoid carbon mutation bugs
        $today = now();

        // Maximum age sets the oldest allowed birthdate (after_or_equal)
        if (!is_null($ageConfig->max_age)) {
            $minDate = $today->copy()->subYears($ageConfig->max_age)->format('Y-m-d');
            $fieldRules[] = "after_or_equal:{$minDate}";
        }
        // Minimum age sets the youngest allowed birthdate (before_or_equal)
        if (!is_null($ageConfig->min_age)) {
            $maxDate = $today->copy()->subYears($ageConfig->min_age)->format('Y-m-d');
            $fieldRules[] = "before_or_equal:{$maxDate}";
        }

        return $fieldRules;
    }

    /**
     * Enclosure Rule Generator: Creates dynamic file validation rules for tab 104 uploads.
     * Compiles file status (required/nullable), MIME types, and maximum KB sizes.
     * 
     * @param array $field Enclosure field schema definition
     * @return array Laravel file validation rules array
     */
    protected function getEnclosingRules(array $field): array
    {
        $fieldRules = [];

        // Check mandate status
        $fieldRules[] = !empty($field['is_required']) ? 'required' : 'nullable';
        $fieldRules[] = 'file';

        // Add allowed MIME extension types
        if (!empty($field['extension_type'])) {
            $fieldRules[] = 'mimes:' . strtolower($field['extension_type']);
        }

        // Add calculated dynamic max file size in Kilobytes
        if (!empty($field['max_file_size'])) {
            $maxKb = $this->parseSizeToKilobytes($field['max_file_size']);
            if ($maxKb > 0) {
                $fieldRules[] = "max:{$maxKb}";
            }
        }

        return $fieldRules;
    }

    /**
     * String Metric Parser: Converts user-friendly size strings like "2MB" or "500KB" 
     * into pure integer Kilobytes expected by Laravel's 'max:' validation rule.
     * 
     * @param string $sizeString Unparsed metric string (e.g., '2MB')
     * @return int Size normalized to Kilobytes
     */
    protected function parseSizeToKilobytes(string $sizeString): int
    {
        $sizeString = strtoupper(trim($sizeString));
        $numericValue = (int) preg_replace('/[^0-9]/', '', $sizeString);

        // Convert Megabytes to Kilobytes
        if (str_contains($sizeString, 'MB')) {
            return $numericValue * 1024;
        }

        // Return base Kilobytes
        return $numericValue;
    }

    /**
     * File Reader: Safely fetches and decodes the storage JSON schema for the current scheme.
     * 
     * @return array Decoded JSON array or empty array if missing
     */
    protected function loadSchemeJsonSchema(): array
    {
        $path = storage_path("app/final_schemes_formdata/scheme_{$this->schemeId}.json");

        if (!File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true) ?? [];
    }

    /**
     * DB Query Execution: Fetches the scheme's active dynamic age parameters.
     * 
     * @return object|null AgeManagements Eloquent model or null
     */
    protected function getAgeManagementConfig(): ?object
    {
        return AgeManagements::where('scheme_id', $this->schemeId)->first();
    }

    /**
     * Baseline Baseline Extension Point: Hook for classes to declare global baseline rules.
     * Can be overridden by intermediate Scheme classes (e.g., MasterTab.php).
     * 
     * @return array Baseline rules applied across all tabs
     */
    protected function getGlobalRules(): array
    {
        return [];
    }

    /**
     * Rule Synthesizer: Combines parsed JSON schema rules with baseline global rules.
     * 
     * @return array Final calculated rule array
     */
    protected function getJsonRules(): array
    {
        return array_merge($this->parseJsonSchemaRules(), $this->getGlobalRules());
    }

    /**
     * Final Security Guard: Trims and sanitizes incoming user array inputs across all schemes.
     * Marked 'final' to prevent child classes from disabling form input sanitization.
     * 
     * @param array $formData Unsafe input key-value array
     * @return array Cleaned input key-value array
     */
    public final function sanitizeFormData(array $formData): array
    {
        return array_map(fn($val) => is_string($val) ? trim($val) : $val, $formData);
    }

    /**
     * Abstract Contract Method: Mandatory method every concrete Tab validation class 
     * must implement to expose its final rules.
     * 
     * @return array Final validation rules array for Livewire/Laravel execution
     */
    abstract public function getRules(): array;
}
