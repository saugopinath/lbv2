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
use App\Http\Controllers\IncompletPageController;
use App\Http\Controllers\WorkFlowController;
use App\Livewire\ProcessApplication\DraftApplicationView;
use App\Http\Controllers\CasteModificationController;
use App\Http\Controllers\MasterParameterSettingController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserPermissionController;
use App\Livewire\MasterParameterSetting\Index as MasterParameterSettingCreate;
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
// Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('filter', [App\Http\Controllers\FilterController::class, 'index'])->middleware(['auth', 'verified'])->name('filter');

Route::resource('cmo-grievances', CMOGrievanceController::class);
Route::get('/beneficiaries_selection', [BeneficiaryListController::class, 'index'])->middleware(['auth', 'verified'])->name('beneficiaries_selection.index')->middleware('auth');
Route::get('/report', [BeneficiaryListController::class, 'show'])->name('report.show');
Route::get('/custom_application/{id}', ApplicationView::class)->name('custom_application.view');

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
// Route::get('/incomplete-types', [IncompleteTypeController::class, 'index'])->middleware(['auth', 'verified'])->name('incomplete.types')->middleware('auth');

Route::get('/incomplete-types/{stage?}', [IncompleteTypeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('incomplete.types');

Route::get('/incomplet-type/{id}', IncompletTypePage::class)
    ->name('incomplet-type.view');


// Route::post('incomplete-full-deatils-update', [IncompleteTypeController::class, 'fullUpdate'])
//     ->middleware(['auth', 'verified'])
//     ->name('incomplete-full-deatils-update');



    Route::post('/incomplete/update/{id}', [IncompleteTypeController::class, 'fullUpdate'])
    ->name('incomplete-full-deatils-update');



// Route::get('/incomplet-type/{id}', [IncompletPageController::class, 'page'])
//     ->name('incomplet-type.page');
Route::get('/viewpage', [DesignController::class, 'viewPage'])->name('viewpage');
Route::get('/approved-lists', [BeneficiaryApprovedListController::class, 'index'])->name('approved-lists');
Route::get('/approved-lists-BA-Wise', [BeneficiaryApprovedListController::class, 'beneficiaryContactwiseList'])->name('approved-lists-BA-Wise');
Route::get('lb-application-list', [WorkFlowController::class, 'index'])->middleware(['auth', 'verified'])->name('lb-application-list');
Route::get('/application/{id}', DraftApplicationView::class)->name('draft-application.view');

Route::get('/permission', [PermissionController::class, 'index'])->name('permission');
Route::get('/user-permission', [UserPermissionController::class, 'index'])->name('user-permission');


Route::get('/assign-users-permissions', AssignPermissionsPage::class)
    ->name('assign-users-permissions');

Route::get('/Caste-modification-info', [CasteModificationController::class, 'index'])->name('Caste-modification-info');
Route::get('/caste-modification/edit', [CasteModificationController::class, 'editview'])->middleware(['auth', 'verified'])->name('caste-modification.edit');
Route::post('/beneficiary/update-caste', [CasteModificationController::class, 'updateCaste'])
     ->name('beneficiary.updateCaste');
Route::get('/caste-modification-list', [CasteModificationController::class, 'list'])->name('caste-modification-list');

Route::get('/view-beneficiary-details', [CasteModificationController::class, 'viewAppDetails'])->name('view-beneficiary-details');
