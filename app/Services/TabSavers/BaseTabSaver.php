<?php

namespace App\Services\TabSavers;

use App\Models\{
    AcceptRejectInfo,
    BeneficiaryAadhaar,
    CmoSmData,
    Codemaster,
    DsPhase
};
use Illuminate\Support\Facades\{
    Auth,
    Cache,
    Crypt,
    DB,
    Schema,
    File
};
use Throwable;

abstract class BaseTabSaver
{
    protected string $schemeId;
    protected string $tabCode;

    public function __construct(string $schemeId, string $tabCode)
    {
        $this->schemeId = $schemeId;
        $this->tabCode = $tabCode;
    }

    /**
     * Main entry point called by the Livewire component.
     */
    public function save(object $component): bool
    {
        if (!$component->applicationId) {
            return false;
        }

        $tab = DB::table('master_tabs')
            ->where('tab_code', $this->tabCode)
            ->first();

        if (!$tab || empty($tab->tab_model_name)) {
            return false;
        }

        $modelClass = "App\\Models\\{$tab->tab_model_name}";
        if (!class_exists($modelClass)) {
            return false;
        }

        // Build Payload
        $dbData = $this->prepareDbPayload($component);

        // Filter valid columns against database schema cache
        $model = new $modelClass;
        $tableName = $model->getTable();
        $columns = Cache::remember(
            "Schema_columns_$tableName",
            86400,
            fn() => Schema::getColumnListing($tableName)
        );

        $dbData = array_intersect_key($dbData, array_flip($columns));

        // Execute save inside transaction
        DB::beginTransaction();
        try {
            $success = $this->executeSave($component, $modelClass, $dbData);
            if ($success) {
                DB::commit();
                return true;
            }
            DB::rollBack();
            return false;
        } catch (Throwable $e) {
            DB::rollBack();
            echo $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Default execution: Handles Update or Create
     */
    protected function executeSave(object $component, string $modelClass, array $dbData): bool
    {
        $existingRecord = $modelClass::where('application_id', $component->applicationId)->first();

        if ($existingRecord) {
            return $this->handleUpdate($component, $existingRecord, $dbData);
        }

        return $this->handleCreate($component, $modelClass, $dbData);
    }

    /**
     * Handles existing record updates
     */
    protected function handleUpdate(object $component, object $existingRecord, array $dbData): bool
    {
        if ($existingRecord->application_type) {
            $dbData['application_type'] = $existingRecord->application_type;
            $dbData['ds_date'] = $existingRecord->ds_date;
            $dbData['ds_registration_no'] = $existingRecord->ds_registration_no;
        }

        $updated = $existingRecord->update($dbData);
        if ($updated) {
            $this->notifySuccess($component, 'Application updated successfully!');
            return true;
        }

        return false;
    }

    /**
     * Handles new record creations (including Aadhaar, Grievance, and Audit trail)
     */
    protected function handleCreate(object $component, string $modelClass, array $dbData): bool
    {
        $created = $modelClass::create($dbData);
        if (!$created) {
            $this->notifyError($component, 'Application not created. Please try again.');
            return false;
        }

        if ($component->isFirst) {
            // Handle Aadhaar verification and Grievance mapping
            if ($component->aadhaarVerified && !empty($component->aadhaarPayload)) {
                BeneficiaryAadhaar::create([
                    'application_id' => $component->applicationId,
                    'beneficiary_id' => $component->beneficiaryId,
                    'scheme_id'     => $this->schemeId,
                    'aadhaar_token' => $component->aadhaarPayload['aadhaar_token'],
                    // 'aadhaar_hash'   => $component->aadhaarPayload['hash'],
                    // 'encoded_aadhaar' => $component->aadhaarPayload['encoded'],
                    'encode_key'     => null,
                    // 'aadhaar_vault'  => $component->aadhaarPayload['hash'],
                ]);

                if ($component->grievanceId) {
                    $grievanceId = Crypt::decryptString($component->grievanceId);
                    CmoSmData::where('id', $grievanceId)->update([
                        'lb_application_id' => $component->applicationId,
                        'is_mark'           => 1,
                    ]);
                }
            }

            // Audit Trail creation
            $acceptRejectInfo = new AcceptRejectInfo();
            $acceptRejectInfo->application_id = $component->applicationId;
            $acceptRejectInfo->beneficiary_id = $component->beneficiaryId;
            $acceptRejectInfo->ip_address = request()->ip();
            $acceptRejectInfo->scheme_id = $this->schemeId;
            $acceptRejectInfo->user_id = Auth::id();
            $acceptRejectInfo->browser = request()->header('User-Agent');
            $acceptRejectInfo->op_type = Codemaster::getIdByCode(2106);
            $acceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $component->applicationId)
                ->latest('id')
                ->value('id');

            if (!$acceptRejectInfo->save()) {
                $this->notifyError($component, 'Application not created. Please try again.');
                return false;
            }
        }

        $this->notifySuccess($component, 'Application created successfully!');
        return true;
    }

    /**
     * Maps $formData according to JSON schema tab fields
     */
    protected function prepareDbPayload(object $component): array
    {
        $path = storage_path("app/final_schemes_formdata/scheme_{$this->schemeId}.json");
        if (!File::exists($path)) {
            return [];
        }

        $json = json_decode(File::get($path), true) ?? [];
        $dbData = [
            'scheme_id' => $this->schemeId,
            'application_id' => $component->applicationId,
            'beneficiary_id' => $component->beneficiaryId,
        ];

        $otherDetails = [];
        foreach ($json['tabs'] ?? [] as $tabJson) {
            if ((string) $tabJson['tab_code'] !== (string) $this->tabCode) {
                continue;
            }
            foreach ($tabJson['fields'] ?? [] as $field) {
                $fieldName = $field['field_name'];
                if (!array_key_exists($fieldName, $component->formData)) {
                    continue;
                }

                if (!empty($field['db_column']) && $field['db_column'] !== 'other_details') {
                    $dbData[$field['db_column']] = $component->formData[$fieldName];
                } elseif (!empty($field['db_column']) && $field['db_column'] === 'other_details') {
                    $otherDetails[$fieldName] = $component->formData[$fieldName];
                }
            }
        }

        if (!empty($otherDetails)) {
            $dbData['other_details'] = json_encode($otherDetails);
        }

        // Auditing & extra system fields
        $extraFields = [
            'created_by_dist_code' => $component->filter_data['created_by_dist_code'] ?? null,
            'created_by_local_body_code' => $component->filter_data['created_by_local_body_code'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ];

        if (!$component->isEdit && !empty($component->formData['ds_registration_no'])) {
            $extraFields['ds_phase'] = DsPhase::where('is_current', true)->value('phase_code');
        }

        return array_merge($dbData, $extraFields);
    }

    protected function notifySuccess(object $component, string $msg): void
    {
        $component->navMessage = "$msg ID: {$component->applicationId}";
        $component->navMessageType = 'success';
        $component->dispatch('toastr', [
            'type' => 'success',
            'message' => "$msg Application ID: {$component->applicationId}",
        ]);
    }

    protected function notifyError(object $component, string $msg): void
    {
        $component->dispatch('toastr', [
            'type' => 'error',
            'message' => $msg,
        ]);
    }
}
