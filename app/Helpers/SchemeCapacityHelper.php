<?php

namespace App\Helpers;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\District;
use App\Models\Scheme;
use App\Models\Subdivision;
use Illuminate\Support\Facades\Crypt;
use Exception;

class SchemeCapacityHelper
{
    protected static $filters = [];
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
    public static function check($schemeId, $actionType, $entryType = 0)
    {
        self::initFilters();
        $entryTypeArr = ($entryType == 0) ? [1, 2] : [(int)$entryType];
        $schemeResult = self::checkScheme($schemeId, $actionType, $entryType, $entryTypeArr);
        if (!$schemeResult['is_processed']) {
            return $schemeResult;
        }
        if (!empty(self::$filters['district_id'])) {
            $districtResult = self::checkDist($schemeId, $actionType, $entryType, $entryTypeArr);
            if (!$districtResult['is_processed']) {
                return $districtResult;
            }
        }
        if (!empty(self::$filters['block_id']) || !empty(self::$filters['subdivision_id'])) {
            $blockSubResult = self::checkBlockSub($schemeId, $actionType, $entryType, $entryTypeArr);
            if (!$blockSubResult['is_processed']) {
                return $blockSubResult;
            }
        }
    }

    public static function checkScheme($schemeId, $actionType, $entryType, $entryTypeArr)
    {
        $scheme = Scheme::with(['capacities' => function ($q) use ($schemeId, $actionType, $entryType) {
            $q->active()
                ->where('model_id', $schemeId)
                ->where('action_type', $actionType)
                ->where(function ($query) use ($entryType) {
                    $query->where('entry_type', $entryType)
                        ->orWhere('entry_type', 0);
                });
        }])->find($schemeId);
        if (!$scheme || !$scheme->capacities->first()) {
            return ['is_processed' => true];
        }
        $capacityRecord = $scheme->capacities->first();
        $total_capacity = (int)$capacityRecord->total_capacity;
        if ($total_capacity > 0) {
            $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
                ->whereIn('is_clean', [1, 2])
                ->when($entryTypeArr, function ($query) use ($entryTypeArr) {
                    return $query->whereIn('application_type', $entryTypeArr);
                })->count();
            if ($total_capacity > $count) {
                return [
                    'is_processed' => true,
                    'total_capacity' => $total_capacity,
                    'already_entered' => $count,
                    'remaining_capacity' => ($total_capacity - $count),
                    'model' => 'Scheme',
                ];
            } else {
                return [
                    'is_processed' => false,
                    'total_capacity' => $total_capacity,
                    'already_entered' => $count,
                    'remaining_capacity' => ($total_capacity - $count),
                    'model' => 'Scheme',
                ];
            }
        }
        return [
            'is_processed' => false,
            'total_capacity' => $total_capacity,
            'already_entered' => 0,
            'remaining_capacity' => 0,
            'model' => 'Scheme',
        ];
    }

    public static function checkDist($schemeId, $actionType, $entryType, $entryTypeArr)
    {
        $currentFilters = self::$filters;
        $districtId = $currentFilters['district_id'];
        $district = District::with(['capacities' => function ($q) use ($schemeId, $actionType, $districtId, $entryType) {
            $q->active()
                ->where('scheme_id', $schemeId)
                ->where('model_id', $districtId)
                ->where('action_type', $actionType)
                ->where(function ($query) use ($entryType) {
                    $query->where('entry_type', $entryType)
                        ->orWhere('entry_type', 0);
                });
        }])->find($districtId);
        if (!$district || !$district->capacities->first()) {
            return ['is_processed' => true];
        }
        $capacityRecord = $district->capacities->first();
        $total_capacity = (int)$capacityRecord->total_capacity;
        if ($total_capacity > 0) {
            $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
                ->where('created_by_dist_code', $districtId)
                ->whereIn('is_clean', [1, 2])
                ->when($entryTypeArr, function ($query) use ($entryTypeArr) {
                    return $query->whereIn('application_type', $entryTypeArr);
                })->count();
            if ($total_capacity > $count) {
                return [
                    'is_processed' => true,
                    'total_capacity' => $total_capacity,
                    'already_entered' => $count,
                    'remaining_capacity' => ($total_capacity - $count),
                    'model' => 'District',
                ];
            } else {
                return [
                    'is_processed' => false,
                    'total_capacity' => $total_capacity,
                    'already_entered' => $count,
                    'remaining_capacity' => ($total_capacity - $count),
                    'model' => 'District',
                ];
            }
        }
        return [
            'is_processed' => false,
            'total_capacity' => $total_capacity,
            'already_entered' => 0,
            'remaining_capacity' => 0,
            'model' => 'District',
        ];
    }

    public static function checkBlockSub($schemeId, $actionType, $entryType, $entryTypeArr)
    {
        $currentFilters = self::$filters;
        $block_id = $currentFilters['block_id'] ?? null;
        $subdivision_id = $currentFilters['subdivision_id'] ?? null;
        if ($block_id) {
            $modelName = 'Block';
            $model_id = $block_id;
        } elseif ($subdivision_id) {
            $modelName = 'Subdivision';
            $model_id = $subdivision_id;
        }
        $fullModelPath = "App\\Models\\" . $modelName;
        $record = $fullModelPath::with(['capacities' => function ($q) use ($schemeId, $actionType, $model_id, $entryType) {
            $q->active()
                ->where('scheme_id', $schemeId)
                ->where('model_id', $model_id)
                ->where('action_type', $actionType)
                ->where(function ($query) use ($entryType) {
                    $query->where('entry_type', $entryType)
                        ->orWhere('entry_type', 0);
                });
        }])->find($model_id);
        if (!$record || !$record->capacities->first()) {
            return ['is_processed' => true];
        }
        $capacityRecord = $record->capacities->first();
        $total_capacity = (int)$capacityRecord->total_capacity;
        if ($total_capacity > 0) {
            $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
                ->where('created_by_dist_code', $currentFilters['district_id'] ?? null)
                ->where('created_by_local_body_code', $model_id)
                ->whereIn('is_clean', [1, 2])
                ->when($entryTypeArr, function ($query) use ($entryTypeArr) {
                    return $query->whereIn('application_type', $entryTypeArr);
                })->count();
            if ($total_capacity > $count) {
                return [
                    'is_processed' => true,
                    'total_capacity' => $total_capacity,
                    'already_entered' => $count,
                    'remaining_capacity' => ($total_capacity - $count),
                    'model' => $modelName,
                ];
            } else {
                return [
                    'is_processed' => false,
                    'total_capacity' => $total_capacity,
                    'already_entered' => $count,
                    'remaining_capacity' => ($total_capacity - $count),
                    'model' => $modelName,
                ];
            }
        }
        return [
            'is_processed' => false,
            'total_capacity' => $total_capacity,
            'already_entered' => 0,
            'remaining_capacity' => 0,
            'model' => $modelName,
        ];
    }
}
