<?php

namespace App\Helpers;

use App\Models\Scheme;
use App\Models\District;
use App\Models\Block;
use App\Models\Subdivision;
use App\Models\BeneficiaryPersonalDetail;
use Illuminate\Support\Facades\Crypt;

class SchemeCapacityHelper
{

    /*
    |--------------------------------------------------------------------------
    | MAIN FUNCTION
    |--------------------------------------------------------------------------
    */

    public static function check($schemeId, $actionType, $entryType = null)
    {

        $filterData = self::getFilterData();

        $baseQuery = self::getBaseQuery($schemeId);

        /*
        |--------------------------------------------------------------------------
        | SCHEME CHECK
        |--------------------------------------------------------------------------
        */

        $result = self::checkScheme($schemeId, $actionType, $entryType, $baseQuery);

        if ($result !== true) {
            return $result;
        }

        /*
        |--------------------------------------------------------------------------
        | DISTRICT CHECK
        |--------------------------------------------------------------------------
        */

        if (!empty($filterData['created_by_dist_code'])) {

            $result = self::checkDistrict(
                $schemeId,
                $actionType,
                $entryType,
                $filterData['created_by_dist_code'],
                $baseQuery
            );

            if ($result !== true) {
                return $result;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | BLOCK CHECK
        |--------------------------------------------------------------------------
        */

        if (!empty($filterData['created_by_local_body_code'])) {

            $result = self::checkBlock(
                $schemeId,
                $actionType,
                $entryType,
                $filterData['created_by_local_body_code'],
                $baseQuery
            );

            if ($result !== true) {
                return $result;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SUBDIVISION CHECK
        |--------------------------------------------------------------------------
        */

        if (!empty($filterData['created_by_subdivision_code'])) {

            $result = self::checkSubdivision(
                $schemeId,
                $actionType,
                $entryType,
                $filterData['created_by_subdivision_code'],
                $baseQuery
            );

            if ($result !== true) {
                return $result;
            }
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | SESSION FILTER DATA
    |--------------------------------------------------------------------------
    */

    private static function getFilterData()
    {
        $filter = [];

        $select_lgd = session('lgd_session');

        if (!empty($select_lgd['district_id'])) {

            $filter['created_by_dist_code'] =
                Crypt::decryptString($select_lgd['district_id']);
        }

        if (!empty($select_lgd['block_id'])) {

            $filter['created_by_local_body_code'] =
                Crypt::decryptString($select_lgd['block_id']);
        }

        if (!empty($select_lgd['subdivision_id'])) {

            $filter['created_by_subdivision_code'] =
                Crypt::decryptString($select_lgd['subdivision_id']);
        }

        return $filter;
    }


    /*
    |--------------------------------------------------------------------------
    | BASE QUERY
    |--------------------------------------------------------------------------
    */

    private static function getBaseQuery($schemeId)
    {
        return BeneficiaryPersonalDetail::where('scheme_id', $schemeId)
            ->whereIn('is_clean', [1, 2]);
    }


    /*
    |--------------------------------------------------------------------------
    | SCHEME CAPACITY CHECK
    |--------------------------------------------------------------------------
    */

    private static function checkScheme($schemeId, $actionType, $entryType, $baseQuery)
    {

        $scheme = Scheme::with([
            'capacities' => function ($q) use ($schemeId, $actionType, $entryType) {

                $q->active()
                    ->where('scheme_id', $schemeId)
                    ->where('action_type', $actionType);

                if ($entryType !== null) {
                    $q->where('entry_type', $entryType);
                }
            }
        ])->find($schemeId);

        if (!$scheme || $scheme->capacities->isEmpty()) {
            return true;
        }

        $capacity = $scheme->capacities->first();

        $count = $baseQuery->count();

        if ($count >= $capacity->total_capacity) {

            return self::makeResponse(
                'Scheme',
                null,
                $capacity->total_capacity,
                $count
            );
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | DISTRICT CAPACITY CHECK
    |--------------------------------------------------------------------------
    */

    private static function checkDistrict($schemeId, $actionType, $entryType, $distId, $baseQuery)
    {

        $district = District::with([
            'capacities' => function ($q) use ($schemeId, $actionType, $entryType) {

                $q->active()
                    ->where('scheme_id', $schemeId)
                    ->where('action_type', $actionType);

                if ($entryType !== null) {
                    $q->where('entry_type', $entryType);
                }
            }
        ])->find($distId);

        if (!$district || $district->capacities->isEmpty()) {
            return true;
        }

        $capacity = $district->capacities->first();

        $count = (clone $baseQuery)
            ->where(District::BENEFICIARY_LOCATION_COLUMN, $distId)
            ->count();

        if ($count >= $capacity->total_capacity) {

            return self::makeResponse(
                'District',
                $distId,
                $capacity->total_capacity,
                $count
            );
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | BLOCK CAPACITY CHECK
    |--------------------------------------------------------------------------
    */

    private static function checkBlock($schemeId, $actionType, $entryType, $blockId, $baseQuery)
    {

        $block = Block::with([
            'capacities' => function ($q) use ($schemeId, $actionType, $entryType) {

                $q->active()
                    ->where('scheme_id', $schemeId)
                    ->where('action_type', $actionType);

                if ($entryType !== null) {
                    $q->where('entry_type', $entryType);
                }
            }
        ])->find($blockId);

        if (!$block || $block->capacities->isEmpty()) {
            return true;
        }

        $capacity = $block->capacities->first();

        $count = (clone $baseQuery)
            ->where(Block::BENEFICIARY_LOCATION_COLUMN, $blockId)
            ->count();

        if ($count >= $capacity->total_capacity) {

            return self::makeResponse(
                'Block',
                $blockId,
                $capacity->total_capacity,
                $count
            );
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | SUBDIVISION CAPACITY CHECK
    |--------------------------------------------------------------------------
    */

    private static function checkSubdivision($schemeId, $actionType, $entryType, $subId, $baseQuery)
    {

        $sub = Subdivision::with([
            'capacities' => function ($q) use ($schemeId, $actionType, $entryType) {

                $q->active()
                    ->where('scheme_id', $schemeId)
                    ->where('action_type', $actionType);

                if ($entryType !== null) {
                    $q->where('entry_type', $entryType);
                }
            }
        ])->find($subId);

        if (!$sub || $sub->capacities->isEmpty()) {
            return true;
        }

        $capacity = $sub->capacities->first();

        $count = (clone $baseQuery)
            ->where(Subdivision::BENEFICIARY_LOCATION_COLUMN, $subId)
            ->count();

        if ($count >= $capacity->total_capacity) {

            return self::makeResponse(
                'Subdivision',
                $subId,
                $capacity->total_capacity,
                $count
            );
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | RESPONSE FORMAT
    |--------------------------------------------------------------------------
    */

    private static function makeResponse($model, $location, $total, $processed)
    {
        return [
            'model' => $model,
            'location' => $location,
            'total' => $total,
            'processed' => $processed,
            'remaining' => max($total - $processed, 0),
        ];
    }
}