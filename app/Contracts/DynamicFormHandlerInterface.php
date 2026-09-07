<?php

namespace App\Contracts;

interface DynamicFormHandlerInterface
{
    // Check if the user is authorized to create/submit entries for the scheme.
    public function authorizeEntry(int $schemeId): bool;

    // Retrieve application type options filtered by user permissions and config.
    public function getPermittedApplicationTypes(int $schemeId, array $rawOptions = []): array;

    // Check capacity availability for the given scheme and application types.
    public function isCapacityAvailable(int $schemeId, int $actionType, array $applicationTypes, bool $isEdit = false, ?string $applicationId = null): array;

    // Perform pre-submission checks (permissions & capacity) before saving.
    public function processSubmission(array $formData, int $schemeId, int $actionType, bool $isEdit, ?string $applicationId): bool;
}
