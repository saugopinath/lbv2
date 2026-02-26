<?php

namespace App\Helpers;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\SchemeCapacity;

class SchemeCapacityHelper
{
    public static function check($schemeId, $actionType, $filterData = [])
    {

        $locations = [

            'District' => [
                'filter' => 'created_by_dist_code',
                'column' => 'created_by_dist_code'
            ],

            'Subdivision' => [
                'filter' => 'created_by_local_body_code',
                'column' => 'created_by_local_body_code'
            ],

            'Block' => [
                'filter' => 'created_by_local_body_code',
                'column' => 'created_by_local_body_code'
            ],

        ];

        foreach ($locations as $model => $config) {

            if (!empty($filterData[$config['filter']])) {

                $capacity = SchemeCapacity::where([
                    'scheme_id' => $schemeId,
                    'action_type' => $actionType,
                    'capacity_type' => 2,
                    'model_type' => "App\\Models\\$model",
                    'model_id' => $filterData[$config['filter']],
                    'is_active' => true
                ])->first();

                if ($capacity) {

                    $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
                        ->where($config['column'], $filterData[$config['filter']])
                        ->count();

                    if ($count >= (int) $capacity->total_capacity) {

                        return [
                            'field' => 'formData.app_type',
                            'message' => "$model capacity full."
                        ];
                    }
                }
            }
        }

        // ===== FULL SCHEME CHECK =====

        $capacity = SchemeCapacity::where([
            'scheme_id' => $schemeId,
            'action_type' => $actionType,
            'capacity_type' => 1,
            'model_type' => 'App\Models\Scheme',
            'is_active' => true
        ])->first();

        if ($capacity) {

            $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)->count();

            if ($count >= (int) $capacity->total_capacity) {

                return [
                    'field' => 'formData.app_type',
                    'message' => 'Scheme capacity full.'
                ];
            }
        }

        return true;
    }
}