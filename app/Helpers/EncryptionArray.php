<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

class EncryptionArray
{

    public static function applyLocationFilter(Builder $query, string $reportType, ?int $district_id, ?int $rural_urban, ?int $blockurban, ?int $gp_ward): Builder
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
    public static function applyLocationFilters(Builder $query, ?int $district_id, ?int $rural_urban, ?int $blockurban, ?int $gp_ward): Builder
    {
        // dump($query);
        // dd($query);
        $blockField  = $rural_urban == 2 ? 'block_id'      : 'municipality_id';
        $gpWardField = $rural_urban == 2 ? 'panchayat_id'  : 'ward_id';

        // $query->with('sourceable.contact');
        // dd($query1->toSql(), $query1->getBindings());
        if ($district_id) {
            $query->where('district_id', $district_id);
        }
        if ($blockurban) {
            $query->where($blockField, $blockurban);
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

        return $query;
    }

    public static function applyLocationFilte(Builder $query, ?int $district_id, ?int $rural_urban, ?int $blockurban, ?int $gp_ward): Builder
    {

        $blockField  = $rural_urban == 2 ? 'block_id'      : 'municipality_id';
        $gpWardField = $rural_urban == 2 ? 'panchayat_id'  : 'ward_id';

        $query->with('sourceable.contact');
        // dd($query->toSql());

        if ($district_id) {
            // dd($district_id);
            $query->whereHas('sourceable.contact', function ($q) use ($district_id) {
                $q->where('district_id', $district_id);
            });
            dd($query->toSql());
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

    // public static function applyLocationFilter(
    //     Builder $query,
    //     ?int $district_id,
    //     ?int $rural_urban,
    //     ?int $blockurban,
    //     ?int $gp_ward
    // ): Builder {
    //     $blockField  = $rural_urban == 2 ? 'block_id' : 'municipality_id';
    //     $gpWardField = $rural_urban == 2 ? 'panchayat_id' : 'ward_id';

    //     // Load relation for eager loading
    //     $query->with('commonList.beneficiaryPersonal.contacts');

    //     if ($district_id) {
    //         $query->whereHas('commonList.beneficiaryPersonal.contacts', function ($q) use ($district_id) {
    //             $q->where('district_id', $district_id);
    //         });
    //     }

    //     if ($blockurban) {
    //         $query->whereHas('commonList.beneficiaryPersonal.contacts', function ($q) use ($blockField, $blockurban) {
    //             $q->where($blockField, $blockurban);
    //         });
    //     }

    //     if ($gp_ward) {
    //         $query->whereHas('commonList.beneficiaryPersonal.contacts', function ($q) use ($gpWardField, $gp_ward) {
    //             $q->where($gpWardField, $gp_ward);
    //         });
    //     }

    //     return $query;
    // }

    public static function applyIncompletLocationFilter(
        Builder $query,
        ?int $district_id,
        ?int $rural_urban,
        ?int $blockurban,
        ?int $gp_ward,
        ?int $filterCode = null
    ): Builder {
        $blockField  = $rural_urban == 2 ? 'block_id' : 'municipality_id';
        $gpWardField = $rural_urban == 2 ? 'panchayat_id' : 'ward_id';

        // Base eager load
        $query->with('commonList.beneficiaryPersonal.contacts');

        // ✅ Priority wise filter (deepest → highest)
        if ($gp_ward) {
            $query->whereHas('commonList.beneficiaryPersonal.contacts', function ($q) use ($gpWardField, $gp_ward) {
                $q->where($gpWardField, $gp_ward);
            });
        } elseif ($blockurban) {
            $query->whereHas('commonList.beneficiaryPersonal.contacts', function ($q) use ($blockField, $blockurban) {
                $q->where($blockField, $blockurban);
            });
        } elseif ($district_id) {
            $query->whereHas('commonList.beneficiaryPersonal.contacts', function ($q) use ($district_id) {
                $q->where('district_id', $district_id);
            });
        } elseif ($filterCode) {
            $query->whereHas('commonList.beneficiaryPersonal.contacts', function ($q) use ($filterCode) {
                $q->where('incomplet_type', $filterCode);
            });
            //  $query->where('incomplet_type', $filterCode);
        }

        return $query;
    }
}
