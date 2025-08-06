<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\UserManagementController;
use App\Livewire\ProcessApplication\DraftApplicationView;
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
// Route::get('lb-application-list', [App\Http\Controllers\LBFormController::class, 'index'])->middleware(['auth', 'verified'])->name('sumittedlist');

Route::resources([
        'roles' => App\Http\Controllers\RoleController::class
    ]);
Route::get('lb-application-list', [App\Http\Controllers\LBFormController::class, 'index'])->middleware(['auth', 'verified'])->name('submitted-list');
Route::get('/draft-application/{id}/edit', [App\Http\Controllers\LBFormController::class, 'edit'])->name('draft-application.edit');
Route::get('/application/{id}/view', DraftApplicationView::class)->name('draft-application.view');
