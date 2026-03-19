<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Home\{HomeController, MapController, BeneficiarySearchController, BeneficiaryTrackController, NotificationController, DashboardController, DemoController};
use Illuminate\Support\Facades\DB;

Route::get('welcome', function () {
    return view('welcome');
});


Route::get('/about', function () {
    return view('about');
});



Route::get('portlet', [MapController::class, 'index'])->name('portlet');
Route::post('/map-district-count', [MapController::class, 'wbDistrictCount'])->name('map.district.count');

Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('scheme_info', [HomeController::class, 'scheme_index'])->name('scheme_info');
Route::get('department', [HomeController::class, 'department_index'])->name('department');

Route::get('track-applicant', [BeneficiarySearchController::class, 'index'])->name('track-applicant');
Route::get('/search', [BeneficiarySearchController::class, 'search']);


Route::get('track-beneficiary', [BeneficiaryTrackController::class, 'trackBeneficiary'])->name('track-beneficiary');
Route::get('/api/beneficiaries/search', [BeneficiaryTrackController::class, 'trackBeneficiaryData'])->name('beneficiaries.search');
Route::get('/track-beneficiary/payment-history/{id}', [BeneficiaryTrackController::class, 'trackBeneficiaryPaymentHistory'])->name('beneficiary.payment.history')->middleware('signed')->withoutMiddleware('throttle');
Route::post('/track-beneficiary/payment-history/error-details', [BeneficiaryTrackController::class, 'getStatusUTRAndErrorFun'])->name('payment.error.details');

Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
Route::post('/notifications/datatable', [NotificationController::class, 'datatable'])->name('notifications.datatable');

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/scheme-wise-applications', [DashboardController::class, 'schemeWiseApplications'])->name('dashboard.schemeWiseApplications');
Route::get('/dashboard/district-wise-beneficiaries', [DashboardController::class, 'districtWiseBeneficiaries'])->name('dashboard.districtWiseBeneficiaries');
Route::get('/age-distribution', [DashboardController::class, 'getAgeDistribution']);
Route::get('/dashboard/consolidated-fy-payments', [DashboardController::class, 'consolidatedFyPayments'])->name('dashboard.fy.consolidated');

// routes/web.php or api.php
Route::get('/chart/scheme-status', function () {
    return DB::connection('pgsql_app_read')->table('home.mv_scheme_status_summary')
        ->orderBy('scheme_id')
        ->get();
});
Route::post('/dashboard/refresh-scheme-status', function () {
    DB::connection('pgsql_app_read')->statement('REFRESH MATERIALIZED VIEW CONCURRENTLY home.mv_scheme_status_summary');
    return response()->json(['status' => 'success']);
})->name('dashboard.refreshSchemeStatus');




Route::get('ben-details/{id}', [BeneficiarySearchController::class, 'ben_details'])->name('ben-details');

Route::get('jbDownload', [BeneficiarySearchController::class, 'viewEncloser'])->name('jbDownload');
