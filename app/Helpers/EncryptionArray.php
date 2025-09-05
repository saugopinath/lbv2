<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

class EncryptionArray
{

    public static function applyLocationFilters(Builder $query, string $reportType, ?int $district_id, ?int $rural_urban, ?int $blockurban, ?int $gp_ward): Builder
    {
        $blockField  = $rural_urban == 2 ? 'block_id'      : 'municipality_id';
        $gpWardField = $rural_urban == 2 ? 'panchayat_id'  : 'ward_id';

        if ($reportType !== "4") {

            $query->with('contact');

            if ($district_id) {
                $query->whereHas('contact', function ($q) use ($district_id) {
                    $q->where('district_id', $district_id);
                });
            }

            if ($blockurban) {
                $query->whereHas('contact', function ($q) use ($blockField, $blockurban) {
                    $q->where($blockField, $blockurban);
                });
            }

            if ($gp_ward) {
                $query->whereHas('contact', function ($q) use ($gpWardField, $gp_ward) {
                    $q->where($gpWardField, $gp_ward);
                });
            }
        } else {

            if ($district_id) {
                $query->where('district_id', $district_id);
            }

            if ($blockurban) {
                $query->where($blockField, $blockurban);
            }

            if ($gp_ward) {
                $query->where($gpWardField, $gp_ward);
            }
        }

        return $query;
    }

    public static function applyLocationFilter(
        Builder $query,
        ?int $district_id,
        ?int $rural_urban,
        ?int $blockurban,
        ?int $gp_ward
    ): Builder {
        $blockField  = $rural_urban == 2 ? 'block_id' : 'municipality_id';
        $gpWardField = $rural_urban == 2 ? 'panchayat_id' : 'ward_id';

        // Load relation for eager loading
        $query->with('commonList');

        if ($district_id) {
            $query->whereHas('commonList', function ($q) use ($district_id) {
                $q->where('district_id', $district_id);
            });
        }

        if ($blockurban) {
            $query->whereHas('commonList', function ($q) use ($blockField, $blockurban) {
                $q->where($blockField, $blockurban);
            });
        }

        if ($gp_ward) {
            $query->whereHas('commonList', function ($q) use ($gpWardField, $gp_ward) {
                $q->where($gpWardField, $gp_ward);
            });
        }

        return $query;
    }
}
