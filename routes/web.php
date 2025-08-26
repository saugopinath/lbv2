<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BeneficiaryApprovedListController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\LBController;
Route::get('/', function () {
    return view('welcome');
});
Route::get('refresh-captcha', [App\Http\Controllers\CaptchaController::class, 'refreshCaptcha'])->name('refresh-captcha');
Route::controller(AuthenticationController::class)->group(function () {
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
Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard')->middleware('auth');
//  Route::resources([
//         'roles' => App\Http\Controllers\RoleController::class
//     ]);
Route::get('/tableDesign', [DesignController::class, 'tableDesign'])->name('tableDesign');
Route::get('/selectionDesign', [DesignController::class, 'selectionDesign'])->name('selectionDesign');
Route::get('lbform', [LBController::class, 'index'])->middleware(['auth', 'verified'])->name('lbform');
Route::get('draftlist', [LBController::class, 'draftlist'])->middleware(['auth', 'verified'])->name('draftlist');
Route::get('draftedit/{id}', [LBController::class, 'draftedit'])->middleware(['auth', 'verified'])->name('draftedit');

Route::get('/viewpage', [DesignController::class, 'viewPage'])->name('viewpage');
Route::get('/approved-lists', [BeneficiaryApprovedListController::class, 'index'])->name('approved-lists');
Route::get('/approved-lists-BA-Wise', [BeneficiaryApprovedListController::class, 'beneficiaryContactwiseList'])->name('approved-lists-BA-Wise');