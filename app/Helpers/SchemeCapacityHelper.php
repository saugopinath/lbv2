<?php

namespace App\Helpers;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\District;
use App\Models\Scheme;
use App\Models\Block;
use App\Models\Subdivision;
use App\Models\WorkflowsteproleMapping;
use Illuminate\Support\Facades\Crypt;
use Exception;
use App\Services\WorkflowService;

class SchemeCapacityHelper
{
    protected static $filters = [];
    protected static $nextLabelRoleIds = [];

    /**
     * সেশন থেকে লোকেশন ফিল্টার ইনিশিয়ালাইজ করা
     */
    private static function initFilters()
    {
        if (!empty(self::$filters)) return;
        $select_lgd = session('lgd_session', []);
        $keys = ['district_id', 'block_id', 'subdivision_id', 'state_id'];
        foreach ($keys as $key) {
            if (!empty($select_lgd[$key])) {
                try {
                    self::$filters[$key] = Crypt::decryptString($select_lgd[$key]);
                } catch (Exception $e) {
                    self::$filters[$key] = null;
                }
            }
        }
    }

    /**
     * প্রধান চেক ফাংশন যা হায়ারার্কি অনুযায়ী কাজ করে
     */
    public static function check($schemeId, $actionType, $entryType = 0, $bencreatAdd = null)
    {
        $map = WorkflowsteproleMapping::getMinMaxWorkflowStep($schemeId)['max'];
        $workflowService = app(WorkflowService::class);
        $labelRoles = $workflowService->getLabelRoles($schemeId, $map);

        // Action Type অনুযায়ী রোল আইডি নির্ধারণ
        if ($actionType == 1) { // Verification
            self::$nextLabelRoleIds = [$labelRoles->same_label_role_id, $labelRoles->next_label_role_id];
        } elseif ($actionType == 2) { // Approval
            self::$nextLabelRoleIds = [$labelRoles->next_label_role_id];
        } else { // Entry
            self::$nextLabelRoleIds = [0, 1, 2, -$schemeId];
        }

        self::initFilters();
        $entryTypeArr = ($entryType == 0) ? [1, 2] : [(int)$entryType];
        
        // ১. Scheme Level Check
        $schemeResult = self::checkScheme($schemeId, $actionType, $entryType, $entryTypeArr);
        if (!$schemeResult['is_processed']) return $schemeResult;

        // ২. District Level Check (স্কিম পাস করলে তবেই চেক হবে)
        $districtResult = self::checkDist($schemeId, $actionType, $entryType, $entryTypeArr, $bencreatAdd);
        if (!$districtResult['is_processed']) return $districtResult;

        // ৩. Block/Subdivision Level Check (ডিস্ট্রিক্ট পাস করলে তবেই চেক হবে)
        $blockSubResult = self::checkBlockSub($schemeId, $actionType, $entryType, $entryTypeArr, $bencreatAdd);
        return $blockSubResult;
    }

    /**
     * Scheme লেভেলে ক্যাপাসিটি চেক
     */
    public static function checkScheme($schemeId, $actionType, $entryType, $entryTypeArr)
    {
        $scheme = Scheme::with(['capacities' => function ($q) use ($schemeId, $actionType, $entryType) {
            $q->active()
                ->where('model_id', $schemeId)
                ->where('action_type', $actionType)
                ->where(function ($query) use ($entryType) {
                    $query->where('entry_type', $entryType)->orWhere('entry_type', 0);
                });
        }])->find($schemeId);

        if (!$scheme || !$scheme->capacities->first()) return ['is_processed' => true];

        return self::calculateRemaining($scheme->capacities->first(), $schemeId, 'Scheme', null, null, $entryTypeArr);
    }

    /**
     * District লেভেলে ক্যাপাসিটি চেক
     */
    public static function checkDist($schemeId, $actionType, $entryType, $entryTypeArr, $bencreatAdd = null)
    {
        $districtId = $bencreatAdd ? $bencreatAdd['created_by_dist_code'] : (self::$filters['district_id'] ?? null);
        if (!$districtId) return ['is_processed' => true];

        $district = District::with(['capacities' => function ($q) use ($schemeId, $actionType, $districtId, $entryType) {
            $q->active()
                ->where('scheme_id', $schemeId)
                ->where('model_id', $districtId)
                ->where('action_type', $actionType)
                ->where(function ($query) use ($entryType) {
                    $query->where('entry_type', $entryType)->orWhere('entry_type', 0);
                });
        }])->find($districtId);

        if (!$district || !$district->capacities->first()) return ['is_processed' => true];

        return self::calculateRemaining($district->capacities->first(), $schemeId, 'District', $districtId, null, $entryTypeArr);
    }

    /**
     * Block/Subdivision লেভেলে ক্যাপাসিটি চেক
     */
    public static function checkBlockSub($schemeId, $actionType, $entryType, $entryTypeArr, $bencreatAdd = null)
    {
        $model_id = null;
        $modelName = '';
        if ($bencreatAdd) {
            $model_id = $bencreatAdd['created_by_local_body_code'];
            $modelName = ($bencreatAdd['creator'] == 1) ? 'Block' : 'Subdivision';
        } else {
            if (!empty(self::$filters['block_id'])) {
                $model_id = self::$filters['block_id'];
                $modelName = 'Block';
            } elseif (!empty(self::$filters['subdivision_id'])) {
                $model_id = self::$filters['subdivision_id'];
                $modelName = 'Subdivision';
            }
        }

        if (!$model_id) return ['is_processed' => true];

        $fullModelPath = "App\\Models\\" . $modelName;
        $record = $fullModelPath::with(['capacities' => function ($q) use ($schemeId, $actionType, $model_id, $entryType) {
            $q->active()
                ->where('scheme_id', $schemeId)
                ->where('model_id', $model_id)
                ->where('action_type', $actionType)
                ->where(function ($query) use ($entryType) {
                    $query->where('entry_type', $entryType)->orWhere('entry_type', 0);
                });
        }])->find($model_id);

        if (!$record || !$record->capacities->first()) return ['is_processed' => true];

        return self::calculateRemaining($record->capacities->first(), $schemeId, $modelName, null, $model_id, $entryTypeArr);
    }

    /**
     * কমন ক্যালকুলেশন লজিক
     */
    private static function calculateRemaining($capacityRecord, $schemeId, $modelLabel, $distCode, $localBodyCode, $entryTypeArr)
    {
        $total_capacity = (int)$capacityRecord->total_capacity;

        // যদি ক্যাপাসিটি ০ হয়, তবে এটাকে আনলিমিটেড ধরে সাকসেস রিটার্ন করবে
        if ($total_capacity <= 0) return ['is_processed' => true];

        $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
            ->whereIn('is_clean', [1, 2])
            ->when($distCode, fn($q) => $q->where('created_by_dist_code', $distCode))
            ->when($localBodyCode, fn($q) => $q->where('created_by_local_body_code', $localBodyCode))
            ->when(self::$nextLabelRoleIds, function ($query, $roles) {
                return $query->whereIn('next_level_role_id', $roles);
            })
            ->whereIn('application_type', $entryTypeArr)
            ->count();

        $remaining = $total_capacity - $count;

        return [
            'is_processed' => ($remaining > 0),
            'total_capacity' => $total_capacity,
            'already_entered' => $count,
            'remaining_capacity' => $remaining,
            'model' => $modelLabel,
        ];
    }

    /**
     * বাল্ক অ্যাকশনের জন্য চেক
     */
    public static function checkBulk($schemeId, $actionType, $applicationIds)
    {
        // যদি আইডি না থাকে তবে ট্রু রিটার্ন করবে
        if (empty($applicationIds)) return ['is_processed' => true];

        // অ্যাপ্লিকেশনের টাইপ বের করা (১ বা ২)
        $apps = BeneficiaryPersonalDetail::whereIn('application_id', $applicationIds)->get();

        // যদি মাল্টিপল টাইপ থাকে তবে ০ ধরবে, নতুবা নির্দিষ্ট টাইপ
        $entryType = $apps->pluck('application_type')->unique()->count() > 1 ? 0 : ($apps->first()->application_type ?? 0);

        // হায়ারার্কি অনুযায়ী চেক কল করা (Scheme > District > Block)
        return self::check($schemeId, $actionType, $entryType);
    }
}
