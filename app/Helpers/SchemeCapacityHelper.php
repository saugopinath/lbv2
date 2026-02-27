<?php

namespace App\Helpers;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\SchemeCapacity;

class SchemeCapacityHelper
{
    public static function check($schemeId, $actionType, $filterData = [])
    {
        $isFinal = null;

        if ($actionType == 1 || $actionType == 2) {
            $isFinal = 1;
        }
// dd($actionType);
        $capacities = SchemeCapacity::where([
            'scheme_id' => $schemeId,
            'action_type' => $actionType,
            'is_active' => true,
        ])->orderBy('id')->get();

        if ($capacities->isEmpty()) {
            return true;
        }

        foreach ($capacities as $capacity) {

            $query = BeneficiaryPersonalDetail::where('scheme_id', $schemeId);

            if ($isFinal) {
                $query->where('is_final', 1)->where('next_level_role_id', $actionType);
            }

            $model = $capacity->modelable;

            /*
            |--------------------------------
            | FULL SCHEME CAPACITY
            |--------------------------------
            */

            if (! $model || ! defined(get_class($model).'::BENEFICIARY_LOCATION_COLUMN')) {

                $count = $query->count();
            }

            /*
            |--------------------------------
            | LOCATION BASED CAPACITY
            |--------------------------------
            */

            else {

                $column = $model::BENEFICIARY_LOCATION_COLUMN;

                if (! isset($filterData[$column])) {
                    continue;
                }

                $query->where($column, $capacity->model_id);

                $count = $query->count();
            }

            $total = (int) $capacity->total_capacity;

            $remaining = max(0, $total - $count);

            if ($count >= $total) {

                return [
                    'model' => class_basename($capacity->model_type),
                    'total' => $total,
                    'processed' => $count,
                    'remaining' => $remaining,
                ];
            }
        }

        return true;
    }
}
