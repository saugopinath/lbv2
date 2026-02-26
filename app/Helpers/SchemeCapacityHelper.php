<?php

namespace App\Helpers;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\SchemeCapacity;

class SchemeCapacityHelper
{

    public static function check($schemeId, $actionType, $filterData = [])
    {

        $capacities = SchemeCapacity::where([
            'scheme_id' => $schemeId,
            'action_type' => $actionType,
            'is_active' => true
        ])->get();

        if ($capacities->isEmpty()) {
            return true;
        }

        foreach ($capacities as $capacity) {

            $modelType = $capacity->model_type;
           
            $modelId   = $capacity->model_id;

            $query = BeneficiaryPersonalDetail::where('scheme_id', $schemeId);

            // ================= FULL SCHEME =================

            if ($modelType === 'App\Models\Scheme') {

                $count = $query->count();

                if ($count >= (int)$capacity->total_capacity) {                   
                    return [
                        'field' => 'formData.app_type',
                        'message' => 'Scheme capacity full.'
                    ];
                }
            }

            // ================= DISTRICT =================

            if ($modelType === 'App\Models\District') {

                $count = $query
                    ->where('created_by_dist_code', $modelId)
                    ->count();

                if ($count >= (int)$capacity->total_capacity) {
                    return [
                        'field' => 'formData.app_type',
                        'message' => 'District capacity full.'
                    ];
                }
            }

            // ================= BLOCK =================

            if ($modelType === 'App\Models\Block') {

                $count = $query
                    ->where('created_by_local_body_code', $modelId)
                    ->count();

                if ($count >= (int)$capacity->total_capacity) {
                    return [
                        'field' => 'formData.app_type',
                        'message' => 'Block capacity full.'
                    ];
                }
            }

            // ================= SUBDIVISION =================

            if ($modelType === 'App\Models\Subdivision') {

                $count = $query
                    ->where('created_by_local_body_code', $modelId)
                    ->count();

                if ($count >= (int)$capacity->total_capacity) {
                    return [
                        'field' => 'formData.app_type',
                        'message' => 'Subdivision capacity full.'
                    ];
                }
            }
        }

        return true;
    }
}