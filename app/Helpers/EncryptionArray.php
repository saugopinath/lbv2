<?php

namespace App\Helpers;

use App\Models\Codemaster;
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
    public static function applyLocationFilters(Builder $query, ?int $district_id, ?int $rural_urban, ?int $blockurban, ?int $gp_ward, ?int $sub_div): Builder
    {
        // dump($sub_div);
        // dd($query);
        $blockField  = $rural_urban == 2 ? 'block_id'      : 'municipality_id';
        $gpWardField = $rural_urban == 2 ? 'panchayat_id'  : 'ward_id';

        // $query->with('sourceable.contact');
        // dd($query1->toSql(), $query1->getBindings());
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

        // dd('fdf');
        // $query->whereHas('sourceable.contact', function ($q) use ($blockField, $blockurban) {
        //     $q->where($blockField, $blockurban);
        // });
        // }

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
        $blockField = $rural_urban == 2 ? 'block_id' : 'municipality_id';
        $gpWardField = $rural_urban == 2 ? 'panchayat_id' : 'ward_id';

        $query->with('commonList.sourceable.contacts');

        if ($gp_ward) {
            $query->whereHas('commonList', function ($q) use ($gpWardField, $gp_ward) {
                $q->whereHasMorph('sourceable', '*', function ($q, $type) use ($gpWardField, $gp_ward) {
                    if (method_exists($q->getModel(), 'contacts')) {
                        $q->whereHas('contacts', function ($subQuery) use ($gpWardField, $gp_ward) {
                            $subQuery->where($gpWardField, $gp_ward);
                        });
                    }
                });
            });
        }
        if ($blockurban) {
            $query->whereHas('commonList', function ($q) use ($blockField, $blockurban) {
                $q->whereHasMorph('sourceable', '*', function ($q, $type) use ($blockField, $blockurban) {
                    if (method_exists($q->getModel(), 'contacts')) {
                        $q->whereHas('contacts', function ($subQuery) use ($blockField, $blockurban) {
                            $subQuery->where($blockField, $blockurban);
                        });
                    }
                });
            });
        }
        if ($district_id) {
            $query->whereHas('commonList', function ($q) use ($district_id) {
                $q->whereHasMorph('sourceable', '*', function ($q, $type) use ($district_id) {
                    if (method_exists($q->getModel(), 'contacts')) {
                        $q->whereHas('contacts', function ($subQuery) use ($district_id) {
                            $subQuery->where('district_id', $district_id);
                        });
                    }
                });
            });
        }
        if ($filterCode) {
            $query->whereHas('commonList', function ($q) use ($filterCode) {
                $q->whereHasMorph('sourceable', '*', function ($q, $type) use ($filterCode) {
                    if (method_exists($q->getModel(), 'contacts')) {
                        $q->whereHas('contacts', function ($subQuery) use ($filterCode) {
                            $subQuery->where('incomplet_type', $filterCode);
                        });
                    }
                });
            });
        }

        return $query;
    }

    // public static function applyIncompletLocationFilter(
    //     Builder $query,
    //     ?int $district_id,
    //     ?int $rural_urban,
    //     ?int $blockurban,
    //     ?int $gp_ward,
    //     ?int $filterCode
    // ): Builder {
    //     // dump($filterCode);
    //     // dd($gp_ward);
    //     $blockField = $rural_urban == 2 ? 'block_id' : 'municipality_id';
    //     $gpWardField = $rural_urban == 2 ? 'panchayat_id' : 'ward_id';

    //     // Eager load relationships
    //     $query->with('commonList.sourceable.contact'); // Adjust to 'contacts' if needed

    //     // Priority-wise filter (deepest → highest)
    //     if ($gp_ward) {
    //         // dump('bjj');
    //         $query->whereHas('commonList', function ($q) use ($gpWardField, $gp_ward) {
    //             $q->whereHasMorph('sourceable', '*', function ($q, $type) use ($gpWardField, $gp_ward) {
    //                 // Only apply the contact filter if the model has a 'contact' relationship
    //                 if (method_exists($q->getModel(), 'contact')) {
    //                     $q->whereHas('contact', function ($subQuery) use ($gpWardField, $gp_ward) {
    //                         $subQuery->where($gpWardField, $gp_ward);
    //                     });
    //                 }
    //             });
    //         });
    //     } if ($blockurban) {
    //         // dump('ddfff');
    //         $query->whereHas('commonList', function ($q) use ($blockField, $blockurban) {
    //             $q->whereHasMorph('sourceable', '*', function ($q, $type) use ($blockField, $blockurban) {
    //                 if (method_exists($q->getModel(), 'contact')) {
    //                     $q->whereHas('contact', function ($subQuery) use ($blockField, $blockurban) {
    //                         $subQuery->where($blockField, $blockurban);
    //                     });
    //                 }
    //             });
    //         });
    //     } if ($district_id) {
    //         // dump('dfdsf');
    //         $query->whereHas('commonList', function ($q) use ($district_id) {
    //             $q->whereHasMorph('sourceable', '*', function ($q, $type) use ($district_id) {
    //                 if (method_exists($q->getModel(), 'contact')) {
    //                     $q->whereHas('contact', function ($subQuery) use ($district_id) {
    //                         $subQuery->where('district_id', $district_id);
    //                     });
    //                 }
    //             });
    //         });
    //     }
    //     // dd($filterCode);
    //     if ($filterCode) {
    //         // dd('dscsfsae');
    //         // dump($filterCode);
    //         // $gjjj=Codemaster::getIdByCode($filterCode);
    //         // dd(Codemaster::getIdByCode($filterCode));
    //         // $query->where('incomplet_type',  $filterCode);


    //         $query->whereHas('commonList', function ($q) use ($filterCode) {
    //             $q->whereHasMorph('sourceable', '*', function ($q, $type) use ($filterCode) {
    //                 if (method_exists($q->getModel(), 'contact')) {
    //                     $q->whereHas('contact', function ($subQuery) use ($filterCode) {
    //                         $subQuery->where('incomplet_type', $filterCode);
    //                     });
    //                 }
    //             });
    //         });

    //     }

    //     return $query;
    // }

    // public function applyIncompletLocationFilter(
    //     Builder $query,
    //     ?int $district_id,
    //     ?int $rural_urban,
    //     ?int $blockurban,
    //     ?int $gp_ward,
    //     ?int $filterCode = null
    // ): Builder {

    //     // Determine correct fields based on rural/urban
    //     $blockField  = $rural_urban === 2 ? 'block_id' : 'municipality_id';
    //     $gpWardField = $rural_urban === 2 ? 'panchayat_id' : 'ward_id';

    //     // Base eager load
    //     $query->with(['commonList.sourceable.contact']);

    //     // Apply filters in priority order: GP/Ward → Block → District → Filter Code
    //     if ($gp_ward) {
    //         $query->whereHasMorph('commonList', '*', function ($q) use ($gpWardField, $gp_ward) {
    //             $q->whereHasMorph('sourceable', '*', function ($sq) use ($gpWardField, $gp_ward) {
    //                 $sq->whereHas('contact', function ($contactQuery) use ($gpWardField, $gp_ward) {
    //                     $contactQuery->where($gpWardField, $gp_ward);
    //                 });
    //             });
    //         });
    //     } elseif ($blockurban) {
    //         $query->whereHasMorph('commonList', '*', function ($q) use ($blockField, $blockurban) {
    //             $q->whereHasMorph('sourceable', '*', function ($sq) use ($blockField, $blockurban) {
    //                 $sq->whereHas('contact', function ($contactQuery) use ($blockField, $blockurban) {
    //                     $contactQuery->where($blockField, $blockurban);
    //                 });
    //             });
    //         });
    //     } elseif ($district_id) {
    //         $query->whereHasMorph('commonList', '*', function ($q) use ($district_id) {
    //             $q->whereHasMorph('sourceable', '*', function ($sq) use ($district_id) {
    //                 $sq->whereHas('contact', function ($contactQuery) use ($district_id) {
    //                     $contactQuery->where('district_id', $district_id);
    //                 });
    //             });
    //         });
    //     }

    //     // Optional filter by code
    //     if ($filterCode) {
    //         $query->where('incomplete_type_code', $filterCode);
    //     }

    //     return $query;
    // }

    public static function applyBackFromJB(
    Builder $query,
    ?int $district_id,
    ?int $rural_urban,
    ?int $blockurban,
    ?int $gp_ward,
    ?int $sub_div
): Builder {

    $blockField  = $rural_urban == 2 ? 'block_id'      : 'municipality_id';
    $gpWardField = $rural_urban == 2 ? 'panchayat_id'  : 'ward_id';

    // DISTRICT
    if ($district_id) {
        $query->whereHas('beneficiary.sourceable.contact', function ($q) use ($district_id) {
            $q->where('district_id', $district_id);
        });
    }

    // BLOCK / MUNICIPALITY
    if ($blockurban) {
        $query->whereHas('beneficiary.sourceable.contact', function ($q) use ($blockField, $blockurban) {
            $q->where($blockField, $blockurban);
        });
    }

    // SUBDIVISION
    if ($sub_div) {
        $query->whereHas('beneficiary.sourceable.contact.municipality', function ($q) use ($sub_div) {
            $q->where('subdivision_id', $sub_div);
        });
    }

    // GP / WARD
    if ($gp_ward) {
        $query->whereHas('beneficiary.sourceable.contact', function ($q) use ($gpWardField, $gp_ward) {
            $q->where($gpWardField, $gp_ward);
        });
    }

    return $query;
}



}
