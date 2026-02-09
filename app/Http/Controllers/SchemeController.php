<?php

namespace App\Http\Controllers;

use App\Models\Scheme;
use App\Models\SchemeFinalSubmitCheck;
use Illuminate\Support\Facades\Route;

class SchemeController extends Controller
{
    public function finalSubmitted()
    {
        $schemes = '';
        $route = Route::currentRouteName();
        if ($route == 'schemes.final-submitted') {
            $schemes = SchemeFinalSubmitCheck::where('is_final_submitted', true)
                ->whereHas('scheme')
                ->with('scheme')
                ->get()
                ->pluck('scheme')
                ->unique('id')
                ->values();
        } else {
            $schemes = Scheme::all();
        }
        return view('schemesblade.dropdown', compact('schemes'));
    }
}
