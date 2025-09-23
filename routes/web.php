<?php

use App\Http\Controllers\CasteModificationController;
use App\Http\Controllers\MasterParameterSettingController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserPermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BeneficiaryApprovedListController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\UserManagementController;
use App\Livewire\MasterParameterSetting\Index as MasterParameterSettingCreate;
use App\Http\Controllers\LBController;
use App\Livewire\UserPermission\AssignPermissionsPage;
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

//master parameter settings
Route::get('/master-parameter-settings', [MasterParameterSettingController::class, 'index'])->name('master-parameter-settings');
Route::get('/masterParameterSetting/index', MasterParameterSettingCreate::class)
    ->name('MasterParameterSetting.index');
// Route::get('/master-edit', [MasterParameterSettingController::class, 'edit'])->name('master-edit');
Route::get('/permission', [PermissionController::class, 'index'])->name('permission');
Route::get('/user-permission', [UserPermissionController::class, 'index'])->name('user-permission');


Route::get('/assign-users-permissions', AssignPermissionsPage::class)
    ->name('assign-users-permissions');

Route::get('/Caste-modification-info', [CasteModificationController::class, 'index'])->name('Caste-modification-info');
Route::post('/caste-modification/edit', [CasteModificationController::class, 'editview'])->name('caste-modification.edit');
Route::post('/beneficiary/update-caste', [CasteModificationController::class, 'updateCaste'])
     ->name('beneficiary.updateCaste');
Route::get('/caste-modification-list', [CasteModificationController::class, 'list'])->name('caste-modification-list');

Route::get('/view-beneficiary-details', [CasteModificationController::class, 'view'])->name('view-beneficiary-details');