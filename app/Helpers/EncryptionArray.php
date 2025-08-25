<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

class EncryptionArray
{

    public static function applyLocationFilters(Builder $query, ?int $district_id, ?int $rural_urban, ?int $blockurban, ?int $gp_ward): Builder
    {
        $blockField  = $rural_urban == 2 ? 'block_id'      : 'municipality_id';
        $gpWardField = $rural_urban == 2 ? 'panchayat_id'  : 'ward_id';

            $query->with('sourceable.contact');

            if ($district_id) {
                $query->whereHas('sourceable.contact', function ($q) use ($district_id) {
                    $q->where('district_id', $district_id);
                });
            }

            if ($blockurban) {
                $query->whereHas('sourceable.contact', function ($q) use ($blockField, $blockurban) {
                    $q->where($blockField, $blockurban);
                });
            }

            if ($gp_ward) {
                $query->whereHas('sourceable.contact', function ($q) use ($gpWardField, $gp_ward) {
                    $q->where($gpWardField, $gp_ward);
                });
            }

        return $query;
    }

    // public static function applyLocationFilters(Builder $query, string $reportType, ?int $district_id, ?int $rural_urban, ?int $blockurban, ?int $gp_ward): Builder
    // {
    //     $blockField = $rural_urban == 2 ? 'block_id' : 'municipality_id';
    //     $gpWardField = $rural_urban == 2 ? 'panchayat_id' : 'ward_id';

    //     $filters = [
    //         ['district_id', $district_id],
    //         [$blockField, $blockurban],
    //         [$gpWardField, $gp_ward],
    //     ];

    //     if ($reportType !== "4") {
    //         $query->with('contact');

    //         foreach ($filters as [$field, $value]) {
    //             if ($value) {
    //                 $query->whereHas('contact', fn($q) => $q->where($field, $value));
    //             }
    //         }
    //     } else {
    //         foreach ($filters as [$field, $value]) {
    //             if ($value) {
    //                 $query->where($field, $value);
    //             }
    //         }
    //     }

    //     return $query;
    // }

    // public static function applyLocationFilter($query, $district_id, $rural_urban, $blockurban, $gp_ward, $reportType)
    // {
    //     if ($reportType === 4) {
    //         if (!empty($district_id)) {
    //             $query->where('district_id', $district_id);
    //         }
    //         if (!empty($rural_urban)) {
    //             $query->where('rural_urban_id', $rural_urban);
    //         }
    //         if (!empty($blockurban)) {
    //             $query->where('block_id', $blockurban);
    //         }
    //         if (!empty($gp_ward)) {
    //             $query->where('gp_ward_id', $gp_ward);
    //         }
    //     }
    //     else {
    //         if (!empty($district_id)) {
    //             $query->whereHas('contact', fn($q) => $q->where('district_id', $district_id));
    //         }
    //         if (!empty($rural_urban)) {
    //             $query->whereHas('contact', fn($q) => $q->where('rural_urban', $rural_urban));
    //         }
    //         if (!empty($blockurban)) {
    //             $query->whereHas('contact', fn($q) => $q->where('block_id', $blockurban));
    //         }
    //         if (!empty($gp_ward)) {
    //             $query->whereHas('contact', fn($q) => $q->where('gp_ward_id', $gp_ward));
    //         }
    //     }

    //     return $query;
    // }
}
