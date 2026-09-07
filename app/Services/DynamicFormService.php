<?php

namespace App\Services;

use App\Contracts\DynamicFormHandlerInterface;
use App\Helpers\SchemeCapacityHelper;
use App\Helpers\WorkFlowPermissionHelper;
use Exception;

class DynamicFormService implements DynamicFormHandlerInterface
{

    // Check if the user is authorized to create/submit entries for the scheme.
    public function authorizeEntry(int $schemeId): bool
    {
        if (!WorkFlowPermissionHelper::canEntry($schemeId)) {
            return false;
        }

        if (!WorkFlowPermissionHelper::canCreateEntry($schemeId)) {
            return false;
        }

        return true;
    }


    // Retrieve application type options filtered by user permissions and doorcamp config.
    public function getPermittedApplicationTypes(int $schemeId, array $rawOptions = []): array
    {
        $options = $rawOptions;

        // Retrieve Door Camp metadata dynamically from config/env
        $doorcampShort = config('applications.doorcamp.doorcamp_short', env('DOORCAMP_SHORT', 'DS'));
        $doorcampFull = config('applications.doorcamp.doorcamp_full', env('DOORCAMP_FULL', 'Duare Sarkar'));
        $doorcampDesc = config('applications.doorcamp.doorcamp_desc', env('DOORCAMP_DESC', 'Duare Sarkar Campaign'));

        if (!WorkFlowPermissionHelper::canNormalEntryAllow($schemeId)) {
            unset($options[1]);
        }

        if (!WorkFlowPermissionHelper::canDuareSarkarEntryAllow($schemeId)) {
            unset($options[2]);
        } else {
            if (isset($options[2])) {
                $options[2] = $doorcampFull;
            }
        }

        return $options;
    }


    // Check capacity availability for the given scheme and application types.
    public function isCapacityAvailable(int $schemeId, int $actionType, array $applicationTypes, bool $isEdit = false, ?string $applicationId = null): array
    {
        if ($isEdit || !empty($applicationId)) {
            return [
                'is_processed' => true,
                'remaining_capacity' => null,
                'model' => 'Scheme',
            ];
        }

        return SchemeCapacityHelper::check($schemeId, $actionType, $applicationTypes);
    }


    // Perform pre-submission checks (permissions & capacity) before saving.
    public function processSubmission(array $formData, int $schemeId, int $actionType, bool $isEdit, ?string $applicationId): bool
    {
        $type = $formData['application_type'] ?? null;

        if (empty($type)) {
            throw new Exception('Application type is required or not authorized.');
        }

        if ($type == 1 && !WorkFlowPermissionHelper::canNormalEntryAllow($schemeId)) {
            throw new Exception('Normal entry is not allowed.');
        }

        if ($type == 2 && !WorkFlowPermissionHelper::canDuareSarkarEntryAllow($schemeId)) {
            $doorcampFull = config('applications.doorcamp.doorcamp_full', env('DOORCAMP_FULL', 'Duare Sarkar'));
            throw new Exception("{$doorcampFull} entry is not allowed.");
        }

        $capacityResult = $this->isCapacityAvailable(
            $schemeId,
            $actionType,
            [(int) $type],
            $isEdit,
            $applicationId
        );

        if (!$capacityResult['is_processed']) {
            $msg = 'Capacity exceeded for ' . ($capacityResult['model'] ?? 'Scheme') .
                '! Available: ' . ($capacityResult['remaining_capacity'] ?? 0);
            throw new Exception($msg);
        }

        return true;
    }
}
