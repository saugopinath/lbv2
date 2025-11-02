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
use App\Livewire\RoleOfficeTypeMappings\Create;
use App\Http\Controllers\CMOGrievanceController;
use App\Http\Controllers\OfficeMastersController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\IncompleteTypeController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\BeneficiaryListController;
use App\Http\Controllers\CasteModificationController;
use App\Http\Controllers\UpdateBankDetailsController;
use App\Http\Controllers\UserDutyManagementController;
use App\Livewire\UserPermission\AssignPermissionsPage;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\PermissionController;
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
        ->middleware('permission.redirect:view users')
        ->name('user-managements.index');

    Route::get('/users/create', UsersCreate::class)
        ->middleware('permission.redirect:create users')
        ->name('users.create');

    // Role & Office Mappings
    Route::get('/role-office-master-mappings', [RoleOfficeTypeMappingsController::class, 'index'])
        ->middleware('permission.redirect:manage role mappings')
        ->name('role-office-master-mappings.index');

    Route::get('/role-office-type-mappings/create', Create::class)
        ->middleware('permission.redirect:create role mappings')
        ->name('role-office-type-mappings.create');

    // Office Masters
    Route::get('/officemasters', [OfficeMastersController::class, 'index'])
        ->middleware('permission.redirect:view offices')
        ->name('officemasters.index');

    Route::get('/office-masters/create', OfficeMasterCreate::class)
        ->middleware('permission.redirect:create offices')
        ->name('office-masters.create');

    // Permissions Management
    Route::get('/permission', [PermissionController::class, 'index'])
        // ->middleware('permission.redirect:view permission')
        ->name('permission');

    Route::get('/user-permission', [UserPermissionController::class, 'index'])
        // ->middleware('permission.redirect:view user permission')
        ->name('user-permission');

    Route::get('/assign-users-permissions', AssignPermissionsPage::class)
        // ->middleware('permission:assign user permission')
        ->name('assign-users-permissions');

    // Duty Management
    Route::get('/userDutymanagement', [UserDutyManagementController::class, 'index'])
        ->middleware('permission.redirect:manage user duties')
        ->name('userDutymanagement.index');

    // LB & Workflow
    Route::get('lbform', [LBController::class, 'index'])
        ->middleware('permission.redirect:submit lb form')
        ->name('lbform');

    Route::get('draftlist', [LBController::class, 'draftlist'])
        ->middleware('permission.redirect:view draft list')
        ->name('draftlist');

    Route::get('draftedit/{id}', [LBController::class, 'draftedit'])
        ->middleware('permission.redirect:edit draft')
        ->name('draftedit');

    Route::get('lb-application-list', [WorkFlowController::class, 'index'])
        ->middleware('permission.redirect:view lb applications')
        ->name('lb-application-list');

    Route::get('/application/{id}', DraftApplicationView::class)
        ->middleware('permission.redirect:view application')
        ->name('draft-application.view');

    // Incomplete Types
    Route::get('/incomplete-types/{stage?}', [IncompleteTypeController::class, 'index'])
        ->middleware('permission.redirect:view incomplete applications')
        ->name('incomplete.types');

    Route::get('/incomplet-type/{id}', IncompletTypePage::class)
        ->name('incomplet-type.view');

    Route::post('/incomplete/update/{id}', [IncompleteTypeController::class, 'fullUpdate'])
        ->middleware('permission.redirect:update incomplete')
        ->name('incomplete-full-deatils-update');

    Route::post('/incomplete/revert/{id}', [IncompleteTypeController::class, 'revertVerify'])
        ->middleware('permission.redirect:revert incomplete')
        ->name('incomplete-revert-update');

    Route::get('/beneficiaries_selection', [BeneficiaryListController::class, 'index'])
        ->middleware('permission.redirect:view beneficiaries')
        ->name('beneficiaries_selection.index');

    Route::get('/report', [BeneficiaryListController::class, 'show'])
        ->middleware('permission.redirect:view reports')
        ->name('report.show');

    Route::get('/approved-lists', [BeneficiaryApprovedListController::class, 'index'])
        ->middleware('permission.redirect:view approved list')
        ->name('approved-lists');

    Route::get('/approved-lists-BA-Wise', [BeneficiaryApprovedListController::class, 'beneficiaryContactwiseList'])
        ->middleware('permission.redirect:view approved ba wise')
        ->name('approved-lists-BA-Wise');

    // Caste & Bank Update
    Route::get('/Caste-modification-info', [CasteModificationController::class, 'index'])
        ->middleware('permission.redirect:modify caste')
        ->name('Caste-modification-info');

    Route::get('/caste-modification/edit', [CasteModificationController::class, 'editview'])
        ->middleware('permission.redirect:edit caste')
        ->name('caste-modification.edit');

    Route::post('/beneficiary/update-caste', [CasteModificationController::class, 'updateCaste'])
        ->middleware('permission.redirect:update caste')
        ->name('beneficiary.updateCaste');

    Route::get('/caste-modification-list', [CasteModificationController::class, 'list'])
        ->middleware('permission.redirect:view caste modification list')
        ->name('caste-modification-list');

    Route::get('/view-beneficiary-details', [CasteModificationController::class, 'viewAppDetails'])
        ->middleware('permission.redirect:view beneficiary details')
        ->name('view-beneficiary-details');

    Route::get('/bankUpdate', [UpdateBankDetailsController::class, 'index'])
        ->middleware('permission.redirect:update bank details')
        ->name('bankUpdate');

    Route::get('/bank-update/search-beneficiary/{type}', [UpdateBankDetailsController::class, 'updateBeneficiaryBank'])
        ->middleware('permission.redirect:search bank update')
        ->name('bank-update.search-beneficiary');

    Route::post('/update-mobile', [UpdateBankDetailsController::class, 'updateMobile'])
        ->middleware('permission.redirect:update mobile')
        ->name('update-mobile');

    Route::post('/update-bank', [UpdateBankDetailsController::class, 'updateBank'])
        ->middleware('permission.redirect:update bank')
        ->name('update-bank');

    // Design Pages (Dev Only – Remove in Prod)
    Route::get('/tableDesign', [DesignController::class, 'tableDesign'])->name('tableDesign');
    Route::get('/selectionDesign', [DesignController::class, 'selectionDesign'])->name('selectionDesign');
    Route::get('/viewpage', [DesignController::class, 'viewPage'])->name('viewpage');
    Route::get('/custom_application/{id}', ApplicationView::class)->name('custom_application.view');
});
