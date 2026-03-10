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
        if (!empty($bencreatAdd)) {
            // যদি ডাটাবেস থেকে বেনিফিশিয়ারি ইনফো আসে
            self::$filters['dist'] = $bencreatAdd['created_by_dist_code'] ?? null;
            self::$filters['local'] = $bencreatAdd['created_by_local_body_code'] ?? null;
            self::$filters['creator'] = $bencreatAdd['creator'] ?? null;
        } else {
            // যদি সরাসরি এন্ট্রি বা সেশন থেকে আসে
            $lgd = session('lgd_session', []);

            self::$filters['dist'] = isset($lgd['district_id'])
                ? \Illuminate\Support\Facades\Crypt::decryptString($lgd['district_id'])
                : null;

            // ব্লক অথবা সাবডিভিশন যেকোনো একটি থাকলে সেটি নিবে
            $blockId = isset($lgd['block_id']) ? \Illuminate\Support\Facades\Crypt::decryptString($lgd['block_id']) : null;
            $subId = isset($lgd['subdivision_id']) ? \Illuminate\Support\Facades\Crypt::decryptString($lgd['subdivision_id']) : null;

            self::$filters['local'] = $blockId ?: $subId;
            self::$filters['creator'] = $blockId ? 1 : 2; // উদাহরণ: ব্লক হলে ১, সাবডিভিশন হলে ২
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
        // dd($res);
        if (!$res['is_processed'])
            return $res;

        $res = self::checkDistrict($schemeId, $actionType, $selectedTypes);
        // dd($res);
        if (!$res['is_processed'])
            return $res;
        
        $res = self::checkLocal($schemeId, $actionType, $selectedTypes);
        // dd($res);
        if (!$res['is_processed'])
            return $res;
        return ['is_processed' => true];
    }

    public static function checkScheme($schemeId, $actionType, $selectedTypes)
    {
        $scheme = Scheme::with([
            'capacities' => function ($q) use ($actionType, $selectedTypes) {
                $q->active()->where('action_type', $actionType)
                    ->where(function ($query) use ($selectedTypes) {
                        $query->whereIn('entry_type', $selectedTypes)->orWhere('entry_type', 0);
                    });
            }
        ])->find($schemeId);

        if (!$scheme || $scheme->capacities->isEmpty())
            return ['is_processed' => true];

        foreach ($scheme->capacities as $capacity) {
            $res = self::calculate($capacity, 'Scheme', $schemeId, null, null, $selectedTypes);
            if (!$res['is_processed'])
                return $res;
        }
        return ['is_processed' => true];
    }

    public static function checkDistrict($schemeId, $actionType, $selectedTypes)
    {
        $distId = self::$filters['dist'];
        if (!$distId)
            return ['is_processed' => true];

        $district = District::with([
            'capacities' => function ($q) use ($schemeId, $actionType, $selectedTypes) {
                $q->active()->where('action_type', $actionType)->where('scheme_id', $schemeId)
                    ->where(function ($query) use ($selectedTypes) {
                        $query->whereIn('entry_type', $selectedTypes)->orWhere('entry_type', 0);
                    });
            }
        ])->find($distId);

        if (!$district || $district->capacities->isEmpty())
            return ['is_processed' => true];

        foreach ($district->capacities as $capacity) {
            $res = self::calculate($capacity, 'District', $schemeId, $distId, null, $selectedTypes);
            if (!$res['is_processed'])
                return $res;
        }
        return ['is_processed' => true];
    }

    public static function checkLocal($schemeId, $actionType, $selectedTypes)
    {
        $localId = self::$filters['local'];
        if (!$localId)
            return ['is_processed' => true];

        $model = (isset(self::$filters['creator']) && self::$filters['creator'] == 1) || session('lgd_session')['block_id']
            ? Block::class : Subdivision::class;

        $localBody = $model::with([
            'capacities' => function ($q) use ($schemeId, $actionType, $selectedTypes) {
                $q->active()->where('action_type', $actionType)->where('scheme_id', $schemeId)
                    ->where(function ($query) use ($selectedTypes) {
                        $query->whereIn('entry_type', $selectedTypes)->orWhere('entry_type', 0);
                    });
            }
        ])->find($localId);

        if (!$localBody || $localBody->capacities->isEmpty())
            return ['is_processed' => true];

        $label = ($model == Block::class) ? 'Block' : 'Subdivision';
        foreach ($localBody->capacities as $capacity) {
            $res = self::calculate($capacity, $label, $schemeId, self::$filters['dist'], $localId, $selectedTypes);
            if (!$res['is_processed'])
                return $res;
        }
        return ['is_processed' => true];
    }

    private static function calculate($capacity, $label, $schemeId, $distId, $localId, $selectedTypes)
    {
        $total = (int) $capacity->total_capacity;
        $dbType = (int) $capacity->entry_type;
        if ($dbType === 0) {
            $currentRequestCount = empty($selectedTypes) ? 1 : count($selectedTypes);
        } else {
            $currentRequestCount = empty($selectedTypes) ? 1 : count(array_filter($selectedTypes, fn($t) => $t == $dbType));
        }

        if ($currentRequestCount === 0)
            return ['is_processed' => true];

        // কুয়েরি শুরু করা হচ্ছে
        $query = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
            ->whereIn('is_clean', [1, 2]);

        // সংশোধন: when() ব্যবহার করে কন্ডিশনাল ফিল্টার যোগ করা
        $query->when(!empty(self::$nextLabelRoleIds), function ($q) {
            return $q->whereIn('next_level_role_id', self::$nextLabelRoleIds);
        });

        if ($dbType === 0) {
            $query->whereIn('application_type', [1, 2]);
        } else {
            $query->where('application_type', $dbType);
        }
        if (self::$filters['dist']) {
            $query->where('created_by_dist_code', self::$filters['dist']);
        }

        if (self::$filters['local']) {
            $query->where('created_by_local_body_code', self::$filters['local']);
        }

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

    // New checkBulk that handles both Session-based and Database-based lookups
    public static function checkBulk($schemeId, $actionType, $applicationIds)
    {
        // Fetch full model instances so we can call the creator() method
        $beneficiaries = BeneficiaryPersonalDetail::whereIn('application_id', $applicationIds)
            ->get(['application_id', 'application_type', 'created_by_dist_code', 'created_by_local_body_code']);

        // Group beneficiaries by their location context
        $groups = [];
        foreach ($beneficiaries as $ben) {
            $creatorType = $ben->creator(); // Calling your model function
            $key = $ben->created_by_dist_code . '|' . $ben->created_by_local_body_code . '|' . $creatorType;

            $groups[$key]['info'] = [
                'created_by_dist_code' => $ben->created_by_dist_code,
                'created_by_local_body_code' => $ben->created_by_local_body_code,
                'creator' => $creatorType
            ];
            $groups[$key]['types'][] = $ben->application_type;
        }
        // dd($groups);
        // Validate each group against capacity
        foreach ($groups as $group) {
            $res = self::check($schemeId, $actionType, $group['types'], $group['info']);

            if (!$res['is_processed']) {
                return $res;
            }
        }

        return ['is_processed' => true];
    }
}
