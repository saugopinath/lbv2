<?php

namespace App\Services\TabSavers;

use App\Services\TabSavers\BaseTabSaver;
use App\Services\TabSavers\DefaultTabSaver;

class TabSaveFactory
{
    /**
     * Resolves and returns a concrete instance of BaseTabSaver.
     *
     * @param string $schemeId
     * @param string $tabCode
     * @return BaseTabSaver
     */
    public static function make(string $schemeId, string $tabCode): BaseTabSaver
    {
        // 1. Level 3 Check: Tab-Scheme Specific Class
        $schemeClass = "App\\Services\\TabSavers\\Tab{$tabCode}\\Tab{$tabCode}Scheme{$schemeId}Saver";
        if (class_exists($schemeClass)) {
            return new $schemeClass($schemeId, $tabCode);
        }

        // 2. Level 2 Check: Master Tab Class
        $masterClass = "App\\Services\\TabSavers\\Tab{$tabCode}\\MasterTab{$tabCode}Saver";
        if (class_exists($masterClass)) {
            return new $masterClass($schemeId, $tabCode);
        }

        // 3. Level 1 Fallback: Concrete Default Saver
        return new DefaultTabSaver($schemeId, $tabCode);
    }
}
