<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Builder;

class EncryptionArray
{

    public static function applyLocationFilters(Builder $query,string $reportType,?int $district_id,?int $rural_urban,?int $blockurban,?int $gp_ward): Builder 
    {
        $blockField = $rural_urban == 2 ? 'block_id' : 'municipality_id';
        $gpWardField = $rural_urban == 2 ? 'panchayat_id' : 'ward_id';

        $filters = [
            ['district_id', $district_id],
            [$blockField, $blockurban],
            [$gpWardField, $gp_ward],
        ];

        if ($reportType !== "4") {
            $query->with('contact');

            foreach ($filters as [$field, $value]) {
                if ($value) {
                    $query->whereHas('contact', fn($q) => $q->where($field, $value));
                }
            }
        } else {
            foreach ($filters as [$field, $value]) {
                if ($value) {
                    $query->where($field, $value);
                }
            }
        }

        return $query;
    }
    public static function lgdsession()
    {

        $lgd_session = [
            'state_id' => Crypt::encrypt('19'),
            // 'district_id' => Crypt::encrypt('305'),
            // 'subdivision_id' => Crypt::encrypt('33903'),
            // 'block_id' => Crypt::encrypt('2793'),
        ];
        return  $lgd_session;
    }

    // public static function applyLocationFilters(
    //     Builder $query,
    //     string $reportType,
    //     ?int $district_id,
    //     ?int $rural_urban,
    //     ?int $blockurban,
    //     ?int $gp_ward
    // ): Builder {

    //     $blockField = $rural_urban == 2 ? 'block_id' : 'municipality_id';
    //     $gpWardField = $rural_urban == 2 ? 'panchayat_id' : 'ward_id';

    //     if ($reportType !== "4") {
    //         $query->with('contact');

    //         if ($district_id) {
    //             $query->whereHas('contact', fn($q) => $q->where('district_id', $district_id));
    //         }

    //         if ($blockurban && $rural_urban) {
    //             $query->whereHas('contact', fn($q) => $q->where($blockField, $blockurban));
    //         }

    //         if ($gp_ward && $rural_urban) {
    //             $query->whereHas('contact', fn($q) => $q->where($gpWardField, $gp_ward));
    //         }
    //     } else {
    //         if ($district_id) {
    //             $query->where('district_id', $district_id);
    //         }

    //         if ($blockurban && $rural_urban) {
    //             $query->where($blockField, $blockurban);
    //         }

    //         if ($gp_ward && $rural_urban) {
    //             $query->where($gpWardField, $gp_ward);
    //         }
    //     }

    //     return $query;
    // }
}
