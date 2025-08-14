<?php

use App\Livewire\ApplicationView;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LBController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CMOGrievanceController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\BeneficiaryListController;


Route::get('/', function () {
    return view('welcome');
});
Route::get('refresh-captcha', [App\Http\Controllers\CaptchaController::class, 'refreshCaptcha'])->name('refresh-captcha');
Route::controller(AuthenticationController::class)->group(function(){
    Route::get('/login', 'login')->name('login');
    Route::post('/loginPost', 'loginCheck')->name('loginPost');
    Route::post('/resendOtp', 'resendOtp')->name('resendOtp');
    Route::get('/otp-validate', 'otpVerification')->middleware(['2fa'])->name('otp-validate');
    Route::post('/otp-validate-post', 'otpValidate')->middleware(['2fa'])->name('otp-validate-post');
    Route::get('/forget-password', 'forgetPassword')->name('forget-password');
    Route::post('/forgetpasswordPost', 'forgetPasswordPost')->name('forgetpasswordPost');
    Route::get('/reset-password', 'resetPassword')->middleware(['2fa'])->name('reset-password');
    Route::post('/resetPasswordPost', 'resetPasswordPost')->middleware(['2fa'])->name('resetPasswordPost');
    Route::post('/logout', 'logout')->name('logout');

});
Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('filter', [App\Http\Controllers\FilterController::class, 'index'])->middleware(['auth', 'verified'])->name('filter');

Route::resource('cmo-grievances', CMOGrievanceController::class);
Route::get('/beneficiaries_selection', [BeneficiaryListController::class, 'index'])->name('beneficiaries_selection.index');
Route::get('/report', [BeneficiaryListController::class, 'show'])->name('report.show');
Route::get('/application/{id}', ApplicationView::class)->name('application.view');

// Route::get('/report/show', ReportPage::class)->name('report.show');

//  Route::resources([
//         'roles' => App\Http\Controllers\RoleController::class
//     ]);

Route::get('lbform', [LBController::class, 'index'])->name('lbform');
