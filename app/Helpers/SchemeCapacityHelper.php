<?php

namespace App\Helpers;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\District;
use App\Models\Scheme;
use App\Models\Block;
use App\Models\Subdivision;
use App\Models\WorkflowsteproleMapping;
use App\Services\WorkflowService;

class SchemeCapacityHelper
{
    protected static $filters = [];
    protected static $nextLabelRoleIds = [];

    private static function initFilters($bencreatAdd = null)
    {
        if ($bencreatAdd) {
            self::$filters['dist'] = $bencreatAdd['created_by_dist_code'] ?? null;
            self::$filters['local'] = $bencreatAdd['created_by_local_body_code'] ?? null;
            self::$filters['creator'] = $bencreatAdd['creator'] ?? null;
        } else {
            $lgd = session('lgd_session', []);
            self::$filters['dist'] = isset($lgd['district_id']) ? \Illuminate\Support\Facades\Crypt::decryptString($lgd['district_id']) : null;
            self::$filters['local'] = isset($lgd['block_id']) ? \Illuminate\Support\Facades\Crypt::decryptString($lgd['block_id']) : (\Illuminate\Support\Facades\Crypt::decryptString($lgd['subdivision_id'] ?? '') ?: null);
        }
    }

    public static function check($schemeId, $actionType, $selectedTypes = [], $bencreatAdd = null)
    {
        $map = WorkflowsteproleMapping::getMinMaxWorkflowStep($schemeId)['max'];
        $workflowService = app(WorkflowService::class);
        $labelRoles = $workflowService->getLabelRoles($schemeId, $map);

        if ($actionType == 1) {
            self::$nextLabelRoleIds = [$labelRoles->same_label_role_id, $labelRoles->next_label_role_id];
        } elseif ($actionType == 2) {
            self::$nextLabelRoleIds = [$labelRoles->next_label_role_id];
        }

        self::initFilters($bencreatAdd);
        $res = self::checkScheme($schemeId, $actionType, $selectedTypes);
        if (!$res['is_processed']) return $res;
        $res = self::checkDistrict($schemeId, $actionType, $selectedTypes);
        if (!$res['is_processed']) return $res;
        $res = self::checkLocal($schemeId, $actionType, $selectedTypes);
        if (!$res['is_processed']) return $res;

        return ['is_processed' => true];
    }

    public static function checkScheme($schemeId, $actionType, $selectedTypes)
    {
        $scheme = Scheme::with(['capacities' => function ($q) use ($actionType, $selectedTypes) {
            $q->active()
                ->where('action_type', $actionType)
                ->where(function ($query) use ($selectedTypes) {
                    $query->whereIn('entry_type', $selectedTypes)
                        ->orwhere('entry_type', 0);
                });
        }])->find($schemeId);
        $capacity = $scheme?->capacities->first();
        if (!$capacity) return ['is_processed' => true];

        return self::calculate($capacity, 'Scheme', $schemeId, null, null, $selectedTypes);
    }

    public static function checkDistrict($schemeId, $actionType, $selectedTypes)
    {
        $distId = self::$filters['dist'];
        if (!$distId) return ['is_processed' => true];
        $district = District::with(['capacities' => function ($q) use ($schemeId, $actionType, $selectedTypes) {
            $q->active()
                ->where('action_type', $actionType)
                ->where('scheme_id', $schemeId)
                ->where(function ($query) use ($selectedTypes) {
                    $query->whereIn('entry_type', $selectedTypes)
                        ->orwhere('entry_type', 0);
                });
        }])->find($distId);
        $capacity = $district?->capacities->first();
        if (!$capacity) return ['is_processed' => true];

        return self::calculate($capacity, 'District', $schemeId, $distId, null, $selectedTypes);
    }

    public static function checkLocal($schemeId, $actionType, $selectedTypes)
    {
        $localId = self::$filters['local'];
        if (!$localId) return ['is_processed' => true];
        if (isset(self::$filters['creator'])) {
            $model = (self::$filters['creator'] == 1) ? Block::class : Subdivision::class;
        } else {
            $model = session('lgd_session')['block_id'] ? Block::class : Subdivision::class;
        }
        $localBody = $model::with(['capacities' => function ($q) use ($schemeId, $actionType, $selectedTypes) {
            $q->active()
                ->where('action_type', $actionType)
                ->where('scheme_id', $schemeId)
                ->where(function ($query) use ($selectedTypes) {
                    $query->whereIn('entry_type', $selectedTypes)
                        ->orwhere('entry_type', 0);
                });
        }])->find($localId);
        $capacity = $localBody?->capacities->first();
        if (!$capacity) return ['is_processed' => true];

        $label = ($model == Block::class) ? 'Block' : 'Subdivision';
        return self::calculate($capacity, $label, $schemeId, self::$filters['dist'], $localId, $selectedTypes);
    }

    private static function calculate($capacity, $label, $schemeId, $distId, $localId, $selectedTypes)
    {
        $total = (int)$capacity->total_capacity;
        $dbType = (int)$capacity->entry_type;
        $currentRequestCount = empty($selectedTypes) ? 1 : count($selectedTypes);
        $query = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
            ->whereIn('is_clean', [1, 2])
            ->whereIn('next_level_role_id', self::$nextLabelRoleIds);

        if ($dbType === 0) $query->whereIn('application_type', [1, 2]);
        else $query->where('application_type', $dbType);
        if ($distId) $query->where('created_by_dist_code', $distId);
        if ($localId) $query->where('created_by_local_body_code', $localId);

        $existingCount = $query->count();

        if (($existingCount + $currentRequestCount) > $total) {
            return [
                'is_processed' => false,
                'total_capacity' => $total,
                'already_entered' => $existingCount,
                'remaining_capacity' => max(0, $total - $existingCount),
                'model' => $label
            ];
        }

        return ['is_processed' => true, 'remaining_capacity' => ($total - $existingCount), 'model' => $label];
    }

    public static function checkBulk($schemeId, $actionType, $selectedTypes = [], $bencreatAdd = null)
    {
        return self::check($schemeId, $actionType, $selectedTypes, $bencreatAdd);
    }
}
