<?php

use App\Livewire\ApplicationView;
use App\Livewire\IncompletTypePage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LBController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\WorkFlowController;
use App\Http\Controllers\DashboardController;
use App\Livewire\Users\Create as UsersCreate;
use App\Http\Controllers\PermissionController;
use App\Livewire\RoleOfficeTypeMappings\Create;
use App\Http\Controllers\CMOGrievanceController;
use App\Http\Controllers\OfficeMastersController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\IncompleteTypeController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\BeneficiaryListController;
use App\Http\Controllers\CasteModificationController;
use App\Http\Controllers\UpdateBankDetailsController;
use App\Http\Controllers\UserDutyManagementController;
use App\Livewire\UserPermission\AssignPermissionsPage;
use App\Livewire\ProcessApplication\DraftApplicationView;
use App\Http\Controllers\MasterParameterSettingController;
use App\Http\Controllers\RoleOfficeTypeMappingsController;
use App\Http\Controllers\BeneficiaryApprovedListController;
use App\Livewire\OfficeMasters\Create as OfficeMasterCreate;

// Guest Routes
Route::get('/', fn() => view('welcome'));
Route::get('refresh-captcha', [App\Http\Controllers\CaptchaController::class, 'refreshCaptcha'])
    ->name('refresh-captcha');

Route::controller(AuthenticationController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/loginPost', 'loginCheck')->name('loginPost');
    Route::post('/resendOtp', 'resendOtp')->name('resendOtp');
    Route::get('/otp-validate', 'otpVerification')->middleware('2fa')->name('otp-validate');
    Route::post('/otp-validate-post', 'otpValidate')->middleware('2fa')->name('otp-validate-post');
    Route::get('/forget-password', 'forgetPassword')->name('forget-password');
    Route::post('/forgetpasswordPost', 'forgetPasswordPost')->name('forgetpasswordPost');
    Route::get('/reset-password', 'resetPassword')->middleware('2fa')->name('reset-password');
    Route::post('/resetPasswordPost', 'resetPasswordPost')->middleware('2fa')->name('resetPasswordPost');
    Route::post('/logout', 'logout')->name('logout');
});

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/user-managements', [UsersController::class, 'index'])
        ->middleware('permission:view users')
        ->name('user-managements.index');

    Route::get('/users/create', UsersCreate::class)
        ->middleware('permission:create users')
        ->name('users.create');

    // Role & Office Mappings
    Route::get('/role-office-master-mappings', [RoleOfficeTypeMappingsController::class, 'index'])
        ->middleware('permission:manage role mappings')
        ->name('role-office-master-mappings.index');

    Route::get('/role-office-type-mappings/create', Create::class)
        ->middleware('permission:create role mappings')
        ->name('role-office-type-mappings.create');

    // Office Masters
    Route::get('/officemasters', [OfficeMastersController::class, 'index'])
        ->middleware('permission:view offices')
        ->name('officemasters.index');

    Route::get('/office-masters/create', OfficeMasterCreate::class)
        ->middleware('permission:create offices')
        ->name('office-masters.create');

    // Permissions
    Route::get('/permission', [PermissionController::class, 'index'])
        // ->middleware('permission:view permissions')
        ->name('permission');

    Route::get('/user-permission', [UserPermissionController::class, 'index'])
        // ->middleware('permission:view user permissions')
        ->name('user-permission');

    Route::get('/assign-users-permissions', AssignPermissionsPage::class)
        // ->middleware('permission:assign permissions')
        ->name('assign-users-permissions');

    // Duty Management
    Route::get('/userDutymanagement', [UserDutyManagementController::class, 'index'])
        ->middleware('permission:manage user duties')
        ->name('userDutymanagement.index');

    // LB & Workflow
    Route::get('lbform', [LBController::class, 'index'])
        ->middleware('permission:submit lb form')
        ->name('lbform');

    Route::get('draftlist', [LBController::class, 'draftlist'])
        ->middleware('permission:view draft list')
        ->name('draftlist');

    Route::get('draftedit/{id}', [LBController::class, 'draftedit'])
        ->middleware('permission:edit draft')
        ->name('draftedit');

    Route::get('lb-application-list', [WorkFlowController::class, 'index'])
        ->middleware('permission:view lb applications')
        ->name('lb-application-list');

    Route::get('/application/{id}', DraftApplicationView::class)
        ->middleware('permission:view application')
        ->name('draft-application.view');

    // Incomplete Types
    Route::get('/incomplete-types/{stage?}', [IncompleteTypeController::class, 'index'])
        ->middleware('permission:view incomplete applications')
        ->name('incomplete.types');

    Route::get('/incomplet-type/{id}', IncompletTypePage::class)
        ->name('incomplet-type.view');

    Route::post('/incomplete/update/{id}', [IncompleteTypeController::class, 'fullUpdate'])
        ->middleware('permission:update incomplete')
        ->name('incomplete-full-deatils-update');

    Route::post('/incomplete/revert/{id}', [IncompleteTypeController::class, 'revertVerify'])
        ->middleware('permission:revert incomplete')
        ->name('incomplete-revert-update');

    // Beneficiary & Reports
    Route::get('/beneficiaries_selection', [BeneficiaryListController::class, 'index'])
        ->middleware('permission:view beneficiaries')
        ->name('beneficiaries_selection.index');

    Route::get('/report', [BeneficiaryListController::class, 'show'])
        ->middleware('permission:view reports')
        ->name('report.show');

    Route::get('/approved-lists', [BeneficiaryApprovedListController::class, 'index'])
        ->middleware('permission:view approved list')
        ->name('approved-lists');

    Route::get('/approved-lists-BA-Wise', [BeneficiaryApprovedListController::class, 'beneficiaryContactwiseList'])
        ->middleware('permission:view approved ba wise')
        ->name('approved-lists-BA-Wise');

    // Caste & Bank Update
    Route::get('/Caste-modification-info', [CasteModificationController::class, 'index'])
        ->middleware('permission:modify caste')
        ->name('Caste-modification-info');

    Route::get('/caste-modification/edit', [CasteModificationController::class, 'editview'])
        ->middleware('permission:edit caste')
        ->name('caste-modification.edit');

    Route::post('/beneficiary/update-caste', [CasteModificationController::class, 'updateCaste'])
        ->middleware('permission:update caste')
        ->name('beneficiary.updateCaste');

    Route::get('/caste-modification-list', [CasteModificationController::class, 'list'])
        ->middleware('permission:view caste modification list')
        ->name('caste-modification-list');

    Route::get('/view-beneficiary-details', [CasteModificationController::class, 'viewAppDetails'])
        ->middleware('permission:view beneficiary details')
        ->name('view-beneficiary-details');

    Route::get('/bankUpdate', [UpdateBankDetailsController::class, 'index'])
        ->middleware('permission:update bank details')
        ->name('bankUpdate');

    Route::get('/bank-update/search-beneficiary/{type}', [UpdateBankDetailsController::class, 'updateBeneficiaryBank'])
        ->middleware('permission:search bank update')
        ->name('bank-update.search-beneficiary');

    Route::post('/update-mobile', [UpdateBankDetailsController::class, 'updateMobile'])
        ->middleware('permission:update mobile')
        ->name('update-mobile');

    Route::post('/update-bank', [UpdateBankDetailsController::class, 'updateBank'])
        ->middleware('permission:update bank')
        ->name('update-bank');

    // Design Pages (Dev Only – Remove in Prod)
    Route::get('/tableDesign', [DesignController::class, 'tableDesign'])->name('tableDesign');
    Route::get('/selectionDesign', [DesignController::class, 'selectionDesign'])->name('selectionDesign');
    Route::get('/viewpage', [DesignController::class, 'viewPage'])->name('viewpage');
    Route::get('/custom_application/{id}', ApplicationView::class)->name('custom_application.view');
});
