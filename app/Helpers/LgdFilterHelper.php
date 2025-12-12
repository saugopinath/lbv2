<?php

namespace App\Helpers;

use App\Models\Block;
use App\Models\District;
use App\Models\Municipality;
use App\Models\Panchayat;
use App\Models\Subdivision;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class LgdFilterHelper
{
    public static function getCodesAndInitialCounts(Request $request): array
    {
        // read session lgd
        $lgd = session('lgd_session') ?? [];
        // prepare helper filter_session as in your example (decrypt strings where present)
        $filter_session = [];
        try {
            if (!empty($lgd['district_id'])) {
                $filter_session['district_id'] = (int) Crypt::decryptString($lgd['district_id']);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            if (!empty($lgd['block_id'])) {
                $filter_session['block_id'] = (int) Crypt::decryptString($lgd['block_id']);
            }
        } catch (\Throwable $e) {
        }

        try {
            if (!empty($lgd['subdivision_id'])) {
                $filter_session['sub_division_id'] = (int) Crypt::decryptString($lgd['subdivision_id']);
            }
        } catch (\Throwable $e) {
        }

        // Initialize codes as null
        $district_code    = null;
        $block_code   = null;
        $muni_code = null;
        $subdivission_code = null;
        $rural_urban_code  = null;
        $gpWard_code       = null;
        // Initialize all count variables visible  to 0 as you requested (visible counts = 0 by default)
        $district_wise_count_visible = 0;
        $block_wise_count_visible = 0;
        $subdivision_wise_count_visible = 0;
        $block_subdivision_wise_count_visible = 0;
        $municipality_wise_count_visible = 0;
        $ward_wise_count_visible = 0;
        $gp_wise_count_visible = 0;
        // district_code:
        // if session has district_id -> district_code = session district_id
        if (!empty($filter_session['district_id'])) {
            $district_code = $filter_session['district_id'];
        } else {
            // else if request has district_id -> assign this district_id into block_code (per spec)
            if ($request->filled('district_id')) {
                $district_code = (int) $request->input('district_id');
            }
        }
        if ($request->filled('rural_urban')) {
            $rural_urban_code = $request->input('rural_urban');
        }
        // block_code:
        // if session has block_id -> block_code = session block_id
        if (!empty($filter_session['block_id'])) {
            $block_code = $filter_session['block_id'];
        } else {
            if ($request->filled('blockurban')) {
                if ($rural_urban_code == 1) {
                    $muni_code = $request->input('blockurban');
                } else {
                    $block_code = $request->input('blockurban');
                }
            }
        }
        // subdivission_code:
        // if session has sub_division_id -> subdivission_code = session sub_division_id
        if (!empty($filter_session['sub_division_id'])) {
            $subdivission_code = $filter_session['sub_division_id'];
        } else {
            // else if request has subdivision_id -> subdivission_code = request subdivision_id
            if ($request->filled('subdivision_id')) {
                $subdivission_code = (int) $request->input('subdivision_id');
            }
        }
        if ($request->filled('gpWard')) {
            $gpWard_code = $request->input('gpWard');
        }

        if ($district_code) {
            $block_subdivision_wise_count_visible = 1;
            if ($rural_urban_code == 1) {
                $subdivision_wise_count_visible = 1;
                $block_subdivision_wise_count_visible = 0;
                if ($subdivission_code) {
                    $municipality_wise_count_visible = 1;
                    $subdivision_wise_count_visible = 0;
                    $block_subdivision_wise_count_visible = 0;
                    if ($muni_code) {
                        $ward_wise_count_visible = 1;
                        $municipality_wise_count_visible = 0;
                        $subdivision_wise_count_visible = 0;
                        $block_subdivision_wise_count_visible = 0;
                    } else {
                        $ward_wise_count_visible = 0;
                        $municipality_wise_count_visible = 1;
                        $subdivision_wise_count_visible = 0;
                        $block_subdivision_wise_count_visible = 0;
                    }
                } else {
                    $subdivision_wise_count_visible = 1;
                    $municipality_wise_count_visible = 0;
                    $block_subdivision_wise_count_visible = 0;
                }
            } elseif ($rural_urban_code == 2) {
                $block_wise_count_visible = 1;
                $block_subdivision_wise_count_visible = 0;
                if ($block_code) {
                    $gp_wise_count_visible = 1;
                    $block_wise_count_visible = 0;
                    $block_subdivision_wise_count_visible = 0;
                } else {
                    $block_wise_count_visible = 1;
                    $block_subdivision_wise_count_visible = 0;
                }
            } else {
                if ($block_code) {
                    $gp_wise_count_visible = 1;
                    $block_wise_count_visible = 0;
                    $block_subdivision_wise_count_visible = 0;
                } elseif ($subdivission_code) {
                    $municipality_wise_count_visible = 1;
                    $subdivision_wise_count_visible = 0;
                    $block_subdivision_wise_count_visible = 0;
                } else {
                    $block_wise_count_visible = 0;
                    $subdivision_wise_count_visible = 0;
                    $block_subdivision_wise_count_visible = 1;
                }
            }
        } else {
            $district_wise_count_visible = 1;
        }

        // if ($district_wise_count_visible == 1) {
        //     $master_model = District::get();
        // } elseif ($block_wise_count_visible == 1) {
        //     $master_model = Block::where('district_id',$district_code)->get();
        // } elseif ($subdivision_wise_count_visible == 1) {
        //     $master_model = Subdivision::where('district_id',$district_code)->get();
        // } elseif ($block_subdivision_wise_count_visible == 1) {
        //     // $master_model = District::
        // } elseif ($municipality_wise_count_visible == 1) {
        //     $master_model = Municipality::where('district_id',$district_code)->get();
        // } elseif ($ward_wise_count_visible == 1) {
        //     $master_model = Ward::where('district_id',$district_code)->get();
        // } elseif ($gp_wise_count_visible == 1) {
        //     $master_model = Panchayat::where('district_id',$district_code)->get();
        // }
        $master_locations = [];
        $mode = null;
        $col  = null;
        $name = null;

        try {
            // Prepare an empty collection to hold model rows in normal cases
            $master_model = collect();

            if ($district_wise_count_visible == 1) {
                // district overview
                $mode = 'district';
                $col  = 'district_id';
                $master_model = District::select('id', 'name')->get();
                $name = 'West Bengal';
            } elseif ($block_wise_count_visible == 1) {
                // blocks for a district
                $mode = 'block';
                $col  = 'block_id';
                $master_model = Block::where('district_id', $district_code)->select('id', 'name')->get();
                $name= District::where('id', $district_code)->first()->name;
            } elseif ($subdivision_wise_count_visible == 1) {
                // subdivisions of district
                $mode = 'subdivision';
                $col  = 'sub_division_id';
                $master_model = Subdivision::where('district_id', $district_code)->select('id', 'name')->get();
                $name= District::where('id', $district_code)->first()->name;
            } elseif ($block_subdivision_wise_count_visible == 1) {
                // combined block-subdivision overview (special case)
                $mode = 'block_subdivision';
                $col  = null; // special mode -> controller will use block_ids & subdivision_ids or parse keys
                $name= District::where('id', $district_code)->first()->name;

                $blocks = Block::where('district_id', $district_code)->select('id', 'name')->get();
                $subdivs = Subdivision::where('district_id', $district_code)->select('id', 'name')->get();

                // build master_locations directly for combined mode using prefixed ids
                foreach ($blocks as $b) {
                    $master_locations[] = [
                        'location_id'   => 'block_' . $b->id,
                        'location_name' => $b->name . ' (Block)',
                    ];
                }
                foreach ($subdivs as $s) {
                    $master_locations[] = [
                        'location_id'   => 'sub_' . $s->id,
                        'location_name' => $s->name . ' (Subdivision)',
                    ];
                }

                // Since we've already populated master_locations for this special mode,
                // skip the generic loop below by setting master_model to empty collection.
                $master_model = collect();
            } elseif ($municipality_wise_count_visible == 1) {
                $mode = 'municipality';
                // if municipalities are stored in block_id column, keep col = 'block_id'
                $col  = 'cd_block_muni_id';
                $master_model = Municipality::where('subdivision_id', $subdivission_code)->select('id', 'name')->get();
                $name= Subdivision::where('id', $subdivission_code)->first()->name;
            } elseif ($ward_wise_count_visible == 1) {
                $mode = 'ward';
                $col  = 'cd_gp_ward_id';
                $master_model = Ward::where('municipality_id', $muni_code)->select('id', 'name')->get();
                $name= Municipality::where('id', $muni_code)->first()->name;
            } elseif ($gp_wise_count_visible == 1) {
                $mode = 'Panchyat';
                $col  = 'cd_gp_ward_id';
                $master_model = Panchayat::where('block_id', $block_code)->select('id', 'name')->get();
                $name= Block::where('id', $block_code)->first()->name;
            }

            // For normal modes (non block_subdivision) populate master_locations from master_model
            if ($master_model && $master_model->isNotEmpty()) {
                foreach ($master_model as $m) {
                    $master_locations[] = [
                        'location_id'   => $m->id,
                        'location_name' => $m->name,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::error('LgdFilterHelper:getCodesAndInitialCounts master fetch error: ' . $e->getMessage());
            $master_locations = [];
            $mode = $mode ?? null;
            $col  = $col ?? null;
        }

        // now $master_locations, $mode and $col are ready to be returned/used

        // can i write this into a single line instade fo write this into each block 
        // Return codes + initial counts + raw filter_session for convenience
        return array_merge([
            'district_code'    => $district_code,
            'block_code'   => $block_code,
            'muni_code' => $muni_code,
            'subdivission_code' => $subdivission_code,
            'rural_urban_code'  => $rural_urban_code,
            'gpWard_code'       => $gpWard_code,
            'district_wise_count_visible' => $district_wise_count_visible,
            'block_wise_count_visible' => $block_wise_count_visible,
            'subdivision_wise_count_visible' => $subdivision_wise_count_visible,
            'block_subdivision_wise_count_visible' => $block_subdivision_wise_count_visible,
            'municipality_wise_count_visible' => $municipality_wise_count_visible,
            'ward_wise_count_visible' => $ward_wise_count_visible,
            'gp_wise_count_visible' => $gp_wise_count_visible,
            'master_locations' => $master_locations,
            'mode' => $mode,
            'col'  => $col,
            'name' => $name,
        ]);
    }
}
