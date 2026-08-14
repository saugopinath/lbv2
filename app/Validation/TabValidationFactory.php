<?php

namespace App\Validation;

use App\Validation\Tabs\BaseTabValidation;

/**
 * Factory Class: Resolves and instantiates the correct validation handler.
 * 
 * Instead of writing hardcoded 'if/else' checks in controllers or Livewire components,
 * this Factory dynamically checks if a scheme-specific tab file exists.
 * If found, it loads that file. If not, it falls back to the master tab file.
 */
class TabValidationFactory
{
    /**
     * Instantiates and returns a validation object matching the given Scheme and Tab.
     *
     * @param string $schemeId  The ID or Code of the current scheme (e.g., '1', '2')
     * @param string $tabCode   The code/identifier of the active tab (e.g., '101', '102')
     * @return BaseTabValidation
     */
    public static function make(string $schemeId, string $tabCode): BaseTabValidation
    {
        // Normalize class names (e.g., tab 101 & scheme 1 become Tab101 & Scheme1)
        $formattedTab = "Tab{$tabCode}";
        $formattedScheme = "Scheme{$schemeId}";

        // Target Path: App\Validation\Tabs\Tab101\Tab101Scheme1Validation
        $schemeSpecificClass = "App\\Validation\\Tabs\\{$formattedTab}\\{$formattedTab}{$formattedScheme}Validation";

        // CHECK 1: Does a custom scheme-specific file exist for this tab?
        // Example: If Tab101Scheme1Validation.php exists, use its custom logic.
        if (class_exists($schemeSpecificClass)) {
            return new $schemeSpecificClass($schemeId, $tabCode);
        }

        // CHECK 2: Fall back to the Master tab file if no custom scheme file exists.
        // Example: App\Validation\Tabs\Tab101\MasterTab101Validation
        $masterClass = "App\\Validation\\Tabs\\{$formattedTab}\\Master{$formattedTab}Validation";

        if (class_exists($masterClass)) {
            return new $masterClass($schemeId, $tabCode);
        }

        // CHECK 3: Generic Emergency Fallback
        // Creates an inline anonymous class extending BaseTabValidation 
        // if neither a custom scheme nor a master file has been created yet.
        return new class($schemeId, $tabCode) extends BaseTabValidation {
            public function getRules(): array
            {
                return $this->getJsonRules();
            }
        };
    }
}
