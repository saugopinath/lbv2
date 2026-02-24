<?php

namespace App\Helpers;

use App\Models\Codemaster;
use Illuminate\Database\Eloquent\Builder;

class EncryptionArray
{

    public static function applyLocationFilte(Builder $query, ?int $district_id, ?int $rural_urban, ?int $blockurban, ?int $gp_ward): Builder
    {

        $blockField = $rural_urban == 2 ? 'block_id' : 'municipality_id';
        $gpWardField = $rural_urban == 2 ? 'panchayat_id' : 'ward_id';

        $query->with('sourceable.contact');
        // dd($query->toSql());

        if ($district_id) {
            // dd($district_id);
            $query->whereHas('sourceable.contact', function ($q) use ($district_id) {
                $q->where('district_id', $district_id);
            });
            // dd($query->toSql());
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

    public static function applyIncompletLocationFilter(
        Builder $query,
        ?int $district_id,
        ?int $rural_urban,
        ?int $blockurban,
        ?int $gpward,
        ?int $sub_div,
        ?int $filterCode
    ): Builder {

        if ($filterCode) {
            $query->where('incomplet_type', $filterCode);
        }

        $query->whereHas('contact', function ($q) use ($district_id, $rural_urban, $blockurban, $gpward, $sub_div) {

            if ($district_id) {
                $q->where('district_id', $district_id);
            }

            if ($sub_div) {
                $q->whereHas('municipality', function ($mq) use ($sub_div) {
                    $mq->where('subdivision_id', $sub_div);
                });
            }

            if ($rural_urban) {
                $q->where('rural_urban', $rural_urban);
            }

            if ($blockurban) {
                $q->where('blockurban', $blockurban);
            }

            if ($gpward) {
                $q->where('gpward', $gpward);
            }

        });

        return $query;
    }
    public static function applyLocationFilters(Builder $query, ?int $district_id, ?int $rural_urban, ?int $blockurban, ?int $gp_ward, ?int $sub_div): Builder
    {
        $query->whereHas('contact', function ($q) use ($district_id, $rural_urban, $blockurban, $gp_ward, $sub_div) {
            if ($district_id) {
                $q->where('district_id', $district_id);
            }
            if ($sub_div) {
                $q->whereHas('municipality', function ($mq) use ($sub_div) {
                    $mq->where('subdivision_id', $sub_div);
                });
            }
            if ($rural_urban) {
                $q->where('rural_urban', $rural_urban);
            }
            if ($blockurban) {
                $q->where('blockurban', $blockurban);
            }
            if ($gp_ward) {
                $q->where('gpward', $gp_ward);
            }
        });
        return $query;
    }
}
