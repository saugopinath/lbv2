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

// Route::post('/auth/bsk-data-entry', function (Request $request) {
//     return response()->json([
//         'is_sendtolb' => true,
//         'message' => 'Data saved successfully',
//         'data' => $request->all()
//     ]);
// });

// Route::post('/auth/bsk-data-entry', function (Request $request) {

//     $token = $request->bearerToken();

//     return response()->json([
//         'token' => $token,
//         'data'  => $request->all()
//     ]);
// });
Route::post('/login', [BskController::class, 'login']);
Route::post('/auth/bsk-data-entry', [BskController::class, 'bskEntryAuthCheck']);

Route::any('/bskUserSessionCreate', [BskController::class, 'bskUserSessionCreate']);

Route::get('bsk-entry-done', function(){ return view('BSKLBFrom/acknowledgement_page'); })->name('bsk-entry-done');

Route::get('/formEntryOption', [BskController::class, 'formEntryOption']);
Route::post('/logout', [BskController::class, 'logout']);
