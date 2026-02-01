<?php

namespace App\Http\Controllers;

use App\Models\SchemeFinalSubmitCheck;

class SchemeController extends Controller
{
    public function finalSubmitted()
    {
        $schemes = SchemeFinalSubmitCheck::where('is_final_submitted', true)
            ->whereHas('scheme')
            ->with('scheme')
            ->get()
            ->pluck('scheme')
            ->unique('id')       
            ->values();

        return view('schemesblade.dropdown', compact('schemes'));
    }
}
