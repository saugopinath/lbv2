<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

class EncryptionArray
{

    public static function applyLocationFilter(Builder $query, string $reportType, ?int $district_id, ?int $rural_urban, ?int $blockurban, ?int $gp_ward, ?int $sub_div): Builder
    {
        // dump($rural_urban);
        // dump($blockurban);
        // dd($gp_ward);
        $blockField  = $rural_urban == 2 ? 'block_id'      : 'municipality_id';
        $gpWardField = $rural_urban == 2 ? 'panchayat_id'  : 'ward_id';
        // $query->with('sourceable.contact');
        // dd($reportType,$district_id,$rural_urban,$blockurban,$gp_ward,$blockField, $gpWardField);
        if ($reportType !== "4") {

            // $query->with('sourceable.contact');
            // dd($query->toSql());

            if ($district_id) {
                $query->whereHasMorph(
                    'sourceable',
                    '*',
                    function ($q) use ($district_id) {
                        $q->whereHas('contact', function ($contactQuery) use ($district_id) {
                            $contactQuery->where('district_id', $district_id);
                        });
                    }
                );
            }
            if ($blockurban) {
                $query->whereHasMorph(
                    'sourceable',
                    '*',
                    function ($q) use ($blockField, $blockurban) {
                        $q->whereHas('contact', function ($subQuery) use ($blockField, $blockurban) {
                            $subQuery->where($blockField, $blockurban);
                        });
                    }
                );
            }
            // dd('fdf');
            // $query->whereHas('sourceable.contact', function ($q) use ($blockField, $blockurban) {
            //     $q->where($blockField, $blockurban);
            // });
            // }
            if ($sub_div) {
                $query->whereHasMorph(
                    'sourceable',
                    '*', // use your morph model(s)
                    function ($q) use ($sub_div) {
                        $q->whereHas('contact.municipality', function ($municipalityQuery) use ($sub_div) {
                            $municipalityQuery->where('subdivision_id', $sub_div);
                        });
                    }
                );
            }
            if ($gp_ward) {
                $query->whereHasMorph(
                    'sourceable',
                    '*',
                    function ($q) use ($gpWardField, $gp_ward) {
                        $q->whereHas('contact', function ($subQuery) use ($gpWardField, $gp_ward) {
                            $subQuery->where($gpWardField, $gp_ward);
                        });
                    }
                );
            }
        } else {

            if ($district_id) {
                $query->where('district_id', $district_id);
            }

            if ($sub_div) {
                $query->where('subdivision_id', $sub_div);
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
