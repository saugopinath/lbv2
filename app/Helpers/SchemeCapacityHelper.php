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
    public static function check($schemeId, $actionType, $entryType = null)
    {
        self::initFilters();
        $entryType = $entryType ?? 0;
        $schemeResult = self::checkScheme($schemeId, $actionType, $entryType);
        if (!$schemeResult['is_processed']) {
            return $schemeResult;
        }
        if (!empty(self::$filters['district_id'])) {
            $districtResult = self::checkDist($schemeId, $actionType, $entryType);
            if (!$districtResult['is_processed']) {
                return $districtResult;
            }
        }
        if (!empty(self::$filters['block_id']) || !empty(self::$filters['subdivision_id'])) {
            $blockSubResult = self::checkBlockSub($schemeId, $actionType, $entryType);
            if (!$blockSubResult['is_processed']) {
                return $blockSubResult;
            }
        }
    }

    public static function checkScheme($schemeId, $actionType, $entryType)
    {
        $scheme = Scheme::with(['capacities' => function ($q) use ($schemeId, $actionType, $entryType) {
            $q->active()
                ->where('model_id', $schemeId)
                ->where('action_type', $actionType)
                ->when($entryType, function ($query) use ($entryType) {
                    return $query->where('entry_type', $entryType);
                });
        }])->find($schemeId);
        $capacityRecord = $scheme->capacities->first();
        if ($capacityRecord) {
            $total_capacity = $capacityRecord->total_capacity;
            if ($total_capacity) {
                $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
                    ->whereIn('is_clean', [1, 2])
                    ->when($entryType, function ($query) use ($entryType) {
                        return $query->where('application_type', $entryType);
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
        } else {
            return [
                'is_processed' => true,
            ];
        }
    }

    public static function checkDist($schemeId, $actionType, $entryType)
    {
        $currentFilters = self::$filters;
        $district = District::with(['capacities' => function ($q) use ($schemeId, $actionType, $currentFilters, $entryType) {
            $q->active()
                ->where('scheme_id', $schemeId)
                ->where('model_id', $currentFilters['district_id'])
                ->where('action_type', $actionType)
                ->when($entryType, function ($query) use ($entryType) {
                    return $query->where('entry_type', $entryType);
                });
        }])->find($currentFilters['district_id']);
        $distcapacityRecord = $district->capacities->first();
        if ($distcapacityRecord) {
            $total_capacity = $distcapacityRecord->total_capacity;
            if ($total_capacity) {
                $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
                    ->where('created_by_dist_code', $currentFilters['district_id'])
                    ->whereIn('is_clean', [1, 2])
                    ->when($entryType, function ($query) use ($entryType) {
                        return $query->where('application_type', $entryType);
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
        } else {
            return [
                'is_processed' => true,
            ];
        }
    }

    public static function checkBlockSub($schemeId, $actionType, $entryType)
    {
        $currentFilters = self::$filters;
        if ($currentFilters['block_id']) {
            $model = 'Block';
            $model_id = $currentFilters['block_id'];
        } else {
            $model = 'Subdivision';
            $model_id = $currentFilters['subdivision_id'];
        }
        $fullModelPath = "App\\Models\\" . $model;
        $BlockSub = $fullModelPath::with(['capacities' => function ($q) use ($schemeId, $actionType, $model_id, $entryType) {
            $q->active()
                ->where('scheme_id', $schemeId)
                ->where('model_id', $model_id)
                ->where('action_type', $actionType)
                ->when($entryType, function ($query) use ($entryType) {
                    return $query->where('entry_type', $entryType);
                });
        }])->find($model_id);
        $BlockSubRecord = $BlockSub->capacities->first();
        if ($BlockSubRecord) {
            $total_capacity = $BlockSubRecord->total_capacity;
            if ($total_capacity) {
                $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
                    ->where('created_by_dist_code', $currentFilters['district_id'])
                    ->where('created_by_local_body_code', $model_id)
                    ->whereIn('is_clean', [1, 2])
                    ->when($entryType, function ($query) use ($entryType) {
                        return $query->where('application_type', $entryType);
                    })->count();
                if ($total_capacity > $count) {
                    return [
                        'is_processed' => true,
                        'total_capacity' => $total_capacity,
                        'already_entered' => $count,
                        'remaining_capacity' => ($total_capacity - $count),
                        'model' => $model,
                    ];
                } else {
                    return [
                        'is_processed' => false,
                        'total_capacity' => $total_capacity,
                        'already_entered' => $count,
                        'remaining_capacity' => ($total_capacity - $count),
                        'model' => $model,
                    ];
                }
            }
        } else {
            return [
                'is_processed' => true,
            ];
        }
    }
}
