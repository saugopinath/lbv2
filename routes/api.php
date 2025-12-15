<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BskController;

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

Route::post('lbapi/auth/login', [AuthController::class, 'login']);
Route::middleware('jwt')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('sendtolb', [AuthController::class, 'sendtolb']);
});

Route::post('/bsk-login', [BskController::class, 'login']);

Route::post('/auth/bsk-data-entry', [BskController::class, 'bskEntryAuthCheck']);

Route::get('bsk-entry-done', function(){ return view('BSKLBFrom/acknowledgement_page'); })->name('bsk-entry-done');

Route::post('/logout', [BskController::class, 'logout']);
