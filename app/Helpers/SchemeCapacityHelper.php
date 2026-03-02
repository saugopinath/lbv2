<?php

namespace App\Helpers;

use App\Models\Scheme;
use App\Models\District;
use App\Models\Block;
use App\Models\Subdivision;
use App\Models\BeneficiaryPersonalDetail;

class SchemeCapacityHelper
{
    public static function check($schemeId, $actionType, $filterData = [])
    {

        /*
           |--------------------------------------------------------------------------
           | BASE QUERY
           |--------------------------------------------------------------------------
           */
        $baseQuery = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
            ->where(function ($q) {
                $q->where('next_level_role_id', '!=', -100)
                    ->orWhereNull('next_level_role_id');
            });
            
        /*
            |--------------------------------------------------------------------------
            | FULL SCHEME FALLBACK
            |--------------------------------------------------------------------------
            */
        $scheme = Scheme::with([
            'capacities' => fn($q) => $q->active()
                ->where('scheme_id', $schemeId)
                ->where('action_type', $actionType)
        ])->find($schemeId);

        if ($scheme && $scheme->capacities->isNotEmpty()) {

            $capacity = $scheme->capacities->first();

            $count = $baseQuery->count();

            if ($count >= $capacity->total_capacity) {

                return [
                    'model' => 'Scheme',
                    'location' => null,
                    'total' => $capacity->total_capacity,
                    'processed' => $count,
                    'remaining' => 0,
                ];
            }
        }
        /*
        |--------------------------------------------------------------------------
        | PRIORITY MAP (Order Matters)
        |--------------------------------------------------------------------------
        */
        $modelPriority = [
            'created_by_local_body_code' => Block::class,
            'created_by_subdivision_code' => Subdivision::class,
            'created_by_dist_code' => District::class,
        ];



        /*
        |--------------------------------------------------------------------------
        | LOOP THROUGH PRIORITY MODELS
        |--------------------------------------------------------------------------
        */
        foreach ($modelPriority as $key => $modelClass) {

            if (empty($filterData[$key])) {
                continue;
            }

            $model = $modelClass::with([
                'capacities' => fn($q) => $q->active()
                    ->where('scheme_id', $schemeId)
                    ->where('action_type', $actionType)
            ])->find($filterData[$key]);

            if (!$model || $model->capacities->isEmpty()) {
                continue;
            }

            $capacity = $model->capacities->first();

            $column = $modelClass::BENEFICIARY_LOCATION_COLUMN;

            $count = (clone $baseQuery)
                ->where($column, $filterData[$key])
                ->count();

            if ($count >= $capacity->total_capacity) {

                return [
                    'model' => class_basename($modelClass),
                    'location' => $filterData[$key],
                    'total' => $capacity->total_capacity,
                    'processed' => $count,
                    'remaining' => 0,
                ];
            }

            // If found but not full, stop checking lower levels
            return true;
        }



        return true;
    }
}