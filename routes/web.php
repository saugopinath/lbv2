<?php

use App\Livewire\ApplicationView;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LBController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CMOGrievanceController;
use App\Http\Controllers\OfficeMastersController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\BeneficiaryListController;
use App\Http\Controllers\UserDutyManagementController;
use App\Http\Controllers\RoleOfficeTypeMappingsController;
use App\Http\Controllers\BeneficiaryApprovedListController;
use App\Livewire\RoleOfficeTypeMappings\Create;
use App\Livewire\OfficeMasters\Create as OfficeMasterCreate;
use App\Livewire\Users\Create as UsersCreate;
use App\Livewire\IncompletTypePage;
use App\Http\Controllers\IncompleteTypeController;


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
// Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('filter', [App\Http\Controllers\FilterController::class, 'index'])->middleware(['auth', 'verified'])->name('filter');

Route::resource('cmo-grievances', CMOGrievanceController::class);
Route::get('/beneficiaries_selection', [BeneficiaryListController::class, 'index'])->middleware(['auth', 'verified'])->name('beneficiaries_selection.index')->middleware('auth');
Route::get('/report', [BeneficiaryListController::class, 'show'])->name('report.show');
Route::get('/application/{id}', ApplicationView::class)->name('application.view');

// Route::get('/report/show', ReportPage::class)->name('report.show');

//  Route::resources([
//         'roles' => App\Http\Controllers\RoleController::class
//     ]);
Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard')->middleware('auth');
//  Route::resources([
//         'roles' => App\Http\Controllers\RoleController::class
//     ]);
Route::get('/tableDesign', [DesignController::class, 'tableDesign'])->name('tableDesign');
Route::get('/selectionDesign', [DesignController::class, 'selectionDesign'])->name('selectionDesign');
Route::get('lbform', [LBController::class, 'index'])->middleware(['auth', 'verified'])->name('lbform');
Route::get('draftlist', [LBController::class, 'draftlist'])->middleware(['auth', 'verified'])->name('draftlist');
Route::get('draftedit/{id}', [LBController::class, 'draftedit'])->middleware(['auth', 'verified'])->name('draftedit');


Route::get('/userDutymanagement', [UserDutyManagementController::class, 'index'])->middleware(['auth', 'verified'])->name('userDutymanagement.index')->middleware('auth');


Route::get('/role-office-master-mappings', [RoleOfficeTypeMappingsController::class, 'index'])->middleware(['auth', 'verified'])->name('role-office-master-mappings.index')->middleware('auth');
Route::get('/role-office-type-mappings/create', Create::class)
    ->name('role-office-type-mappings.create');

Route::get('/officemasters', [OfficeMastersController::class, 'index'])->middleware(['auth', 'verified'])->name('officemasters.index')->middleware('auth');
Route::get('/office-masters/create', OfficeMasterCreate::class)
    ->name('office-masters.create');

Route::get('/user-managements', [UsersController::class, 'index'])->middleware(['auth', 'verified'])->name('user-managements.index')->middleware('auth');
Route::get('/users/create', UsersCreate::class)
    ->name('users.create');

// Route::get('/incomplete-types', IncompleteType::class)->name('incomplete.types');
Route::get('/incomplete-types', [IncompleteTypeController::class, 'index'])->middleware(['auth', 'verified'])->name('incomplete.types')->middleware('auth');
Route::get('/incomplet-type/{id}', IncompletTypePage::class)
    ->name('incomplet-type.view');

