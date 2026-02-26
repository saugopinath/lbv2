<?php

namespace App\Helpers;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\SchemeCapacity;

class SchemeCapacityHelper
{

    public static function check($schemeId, $actionType, $filterData = [])
    {

        // ================= DISTRICT =================

        if (!empty($filterData['created_by_dist_code'])) {

            $capacity = SchemeCapacity::where([
                'scheme_id' => $schemeId,
                'action_type' => $actionType,
                'capacity_type' => 2,
                'model_type' => 'App\Models\District',
                'model_id' => $filterData['created_by_dist_code'],
                'is_active' => true
            ])->first();

            if ($capacity) {

                $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
                    ->where('created_by_dist_code', $filterData['created_by_dist_code'])
                    ->count();

                if ($count >= (int)$capacity->total_capacity) {

                    return [
                        'field' => 'formData.app_type',
                        'message' => 'District capacity full.'
                    ];
                }
            }
        }

        // ================= SUBDIVISION =================

        if (!empty($filterData['created_by_subdivision_code'])) {

            $capacity = SchemeCapacity::where([
                'scheme_id' => $schemeId,
                'action_type' => $actionType,
                'capacity_type' => 2,
                'model_type' => 'App\Models\Subdivision',
                'model_id' => $filterData['created_by_subdivision_code'],
                'is_active' => true
            ])->first();

            if ($capacity) {

                $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
                    ->where('created_by_subdivision_code', $filterData['created_by_subdivision_code'])
                    ->count();

                if ($count >= (int)$capacity->total_capacity) {

                    return [
                        'field' => 'formData.app_type',
                        'message' => 'Subdivision capacity full.'
                    ];
                }
            }
        }

        // ================= BLOCK =================

        if (!empty($filterData['created_by_local_body_code'])) {

            $capacity = SchemeCapacity::where([
                'scheme_id' => $schemeId,
                'action_type' => $actionType,
                'capacity_type' => 2,
                'model_type' => 'App\Models\Block',
                'model_id' => $filterData['created_by_local_body_code'],
                'is_active' => true
            ])->first();

            if ($capacity) {

                $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
                    ->where('created_by_local_body_code', $filterData['created_by_local_body_code'])
                    ->count();

                if ($count >= (int)$capacity->total_capacity) {

                    return [
                        'field' => 'formData.app_type',
                        'message' => 'Block capacity full.'
                    ];
                }
            }
        }

        // ================= FULL SCHEME =================

        $capacity = SchemeCapacity::where([
            'scheme_id' => $schemeId,
            'action_type' => $actionType,
            'capacity_type' => 1,
            'model_type' => 'App\Models\Scheme',
            'is_active' => true
        ])->first();

        if ($capacity) {

            $count = BeneficiaryPersonalDetail::where('scheme_id', $schemeId)->count();

            if ($count >= (int)$capacity->total_capacity) {

                return [
                    'field' => 'formData.app_type',
                    'message' => 'Scheme capacity full.'
                ];
            }
        }

        return true;
    }
}