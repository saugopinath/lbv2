<?php

use App\Http\Controllers\RejectApprovedBeneficiaryController;
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
use App\Http\Controllers\CmoController;
use App\Http\Controllers\CreateAssignOtherFormFieldController;
use App\Http\Controllers\DynamicFormController;
use App\Http\Controllers\RolePermisssionManagementController;
use App\Http\Controllers\ElasticSearchController;
use App\Http\Controllers\MarkedUpdateBeneficiary;
use App\Http\Controllers\MarkedUpdateBeneficiaryController;
use App\Livewire\OfficeMasters\Create as OfficeMasterCreate;
use App\Http\Controllers\MisReportController;
use App\Livewire\SchemeTabFieldManager;

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
        ->middleware('permission.redirect:canViewUser')
        ->name('user-managements.index');

    Route::get('/users/create', UsersCreate::class)
        ->middleware('permission.redirect:canCreateUsers')
        ->name('users.create');

    // Role & Office Mappings
    Route::get('/role-office-master-mappings', [RoleOfficeTypeMappingsController::class, 'index'])
        ->middleware('permission.redirect:canRoleMapping')
        ->name('role-office-master-mappings.index');

    Route::get('/role-office-type-mappings/create', Create::class)
        ->middleware('permission.redirect:canRoleMappings')
        ->name('role-office-type-mappings.create');

    // Office Masters
    Route::get('/officemasters', [OfficeMastersController::class, 'index'])
        ->middleware('permission.redirect:canViewOffices')
        ->name('officemasters.index');

    Route::get('/office-masters/create', OfficeMasterCreate::class)
        ->middleware('permission.redirect:canCreateOffices')
        ->name('office-masters.create');

    // Permissions Management
    Route::get('/permission', [PermissionController::class, 'index'])
        ->middleware('permission.redirect:canViewPermission')
        ->name('permission');

    Route::get('/user-permission', [UserPermissionController::class, 'index'])
        ->middleware('permission.redirect:canViewUserPermisson')
        ->name('user-permission');

    Route::get('/assign-users-permissions', AssignPermissionsPage::class)
        ->name('assign-users-permissions');

    Route::get('/role-permission-management', [RolePermisssionManagementController::class, 'index'])
        ->name('role-permission-management');

    // Duty Management
    Route::get('/userDutymanagement', [UserDutyManagementController::class, 'index'])
        ->middleware('permission.redirect:manage user duties')
        ->name('userDutymanagement.index');

    // LB & Workflow
    Route::post('/select-scheme', [LBController::class, 'selectScheme'])
    ->name('select-scheme');

    Route::get('lbform', [LBController::class, 'index'])
        ->middleware('permission.redirect:canEntry')
        ->name('lbform');

    Route::get('draftlist', [LBController::class, 'draftlist'])
        ->middleware('permission.redirect:canDraftList')
        ->name('draftlist');

    Route::get('draftedit/{id}', [LBController::class, 'draftedit'])
        ->middleware('permission.redirect:canEditDraft')
        ->name('draftedit');

    Route::get('lb-application-list', [WorkFlowController::class, 'index'])
        ->middleware('permission.redirect:canViewLbApplications')
        ->name('lb-application-list');

    Route::get('/application/{id}', DraftApplicationView::class)
        ->name('draft-application.view');

    // Incomplete Types
    Route::get('/incomplete-types/{stage?}', [IncompleteTypeController::class, 'index'])
        ->name('incomplete.types');

    Route::get('/incomplet-type/{id}', IncompletTypePage::class)
        ->name('incomplet-type.view');

    Route::post('/incomplete/update/{id}', [IncompleteTypeController::class, 'fullUpdate'])
        ->middleware('permission.redirect:canUpdateIncomplet')
        ->name('incomplete-full-deatils-update');

    Route::post('/incomplete/revert/{id}', [IncompleteTypeController::class, 'revertVerify'])
        ->middleware('permission.redirect:canRevertIncomplet')
        ->name('incomplete-revert-update');

    Route::get('/beneficiaries_selection', [BeneficiaryListController::class, 'index'])
        ->middleware('permission.redirect:canViewBeneficiaries')
        ->name('beneficiaries_selection.index');

    Route::get('/report', [BeneficiaryListController::class, 'show'])
        ->middleware('permission.redirect:canViewReport')
        ->name('report.show');

    Route::get('/approved-lists', [BeneficiaryApprovedListController::class, 'index'])
        ->middleware('permission.redirect:canViewApprovedList')
        ->name('approved-lists');

    Route::get('/approved-lists-BA-Wise', [BeneficiaryApprovedListController::class, 'beneficiaryContactwiseList'])
        ->middleware('permission.redirect:canApprovedWise')
        ->name('approved-lists-BA-Wise');

    // Caste & Bank Update
    Route::get('/Caste-modification-info', [CasteModificationController::class, 'index'])
        ->middleware('permission.redirect:canModifyCaste')
        ->name('Caste-modification-info');

    Route::get('/caste-modification/edit', [CasteModificationController::class, 'editview'])
        // ->middleware('permission.redirect:canEditCaste')
        ->name('caste-modification.edit');

    Route::post('/beneficiary/update-caste', [CasteModificationController::class, 'updateCaste'])
        // ->middleware('permission.redirect:canUpdateCaste')
        ->name('beneficiary.updateCaste');

    Route::get('/caste-modification-list', [CasteModificationController::class, 'list'])
        ->middleware('permission.redirect:canCasteModification')
        ->name('caste-modification-list');

    Route::get('/view-beneficiary-details', [CasteModificationController::class, 'viewAppDetails'])
        // ->middleware('permission.redirect:canBeneficiaryDetails')
        ->name('view-beneficiary-details');

    Route::get('/bankUpdate', [UpdateBankDetailsController::class, 'index'])
        ->middleware('permission.redirect:canUpdateBankDetails')
        ->name('bankUpdate');

    Route::get('/bank-update/search-beneficiary/{type}', [UpdateBankDetailsController::class, 'updateBeneficiaryBank'])
        ->middleware('permission.redirect:canSearchBankUpdate')
        ->name('bank-update.search-beneficiary');

    Route::post('/update-mobile', [UpdateBankDetailsController::class, 'updateMobile'])
        ->middleware('permission.redirect:canUpdateMobile')
        ->name('update-mobile');

    Route::post('/update-bank', [UpdateBankDetailsController::class, 'updateBank'])
        ->middleware('permission.redirect:canUpdateBank')
        ->name('update-bank');

    Route::get('/mis-report', [MisReportController::class, 'index'])->name('mis.index');
    // Route::post('/mis-report-data', [MisReportController::class, 'getData'])->name('mis.data');

    // Design Pages (Dev Only – Remove in Prod)
    Route::get('/tableDesign', [DesignController::class, 'tableDesign'])->name('tableDesign');
    Route::get('/selectionDesign', [DesignController::class, 'selectionDesign'])->name('selectionDesign');
    Route::get('/viewpage', [DesignController::class, 'viewPage'])->name('viewpage');
    Route::get('/custom_application/{id}', ApplicationView::class)->name('custom_application.view');
    Route::get('/getelsticsearchIndex', [ElasticSearchController::class, 'index'])->name('getelsticsearchIndex');
});
Route::controller(CmoController::class)->group(function () {
    Route::any('/pullnewcmo', 'pullnewcmo')->name('pullnewcmo');
    Route::any('/populatelbportal', 'populatelbportal')->name('populatelbportal');
    Route::any('/cmo-grievance-workflow', 'cmogrievanceworkflow')->name('cmo-grievance-workflow');
    // Route::any('/cmo-grievance-find/{id}', 'cmogrievancefind')->name('cmo-grievance-find');
    Route::any('/cmo-grievance-find', 'cmogrievancefind')->name('cmo-grievance-find');
    Route::post('/cmo-grievance-action', 'cmodetailsaction')->name('cmo-grievance-action');
    Route::post('/cmo-grievance-search', 'cmogrievancesearch')->name('cmo-grievance-search');
    Route::post('/map-applicant', 'mapapplicant')->name('map-applicant');
    Route::post('/cmo-add-actions', 'addactions')->name('cmo-add-actions');
});
//reject approved beneficiary
Route::controller(RejectApprovedBeneficiaryController::class)->group(function () {
    Route::get('/reject-approved-beneficiary',  'index')
        ->middleware('permission.redirect:canRejectApprovedBeneficiary')
        ->name('reject-approved-beneficiary');
    Route::get('/reject-approved-beneficiary/de-activate', 'editview')->middleware('permission.redirect:canViewDetailsToReject')->name('reject-approved-beneficiary.de-activate');
    Route::post('/deActivebeneficiary', 'deActiveBeneficiary')->middleware('permission.redirect:canRejectBeneficiary')->name('beneficiary.deActivebeneficiary');
});

Route::controller(MarkedUpdateBeneficiaryController::class)->group(function () {
    Route::get('/marked-beneficiary',  'index')
        ->name('marked-beneficiary');
    Route::get('/mark-beneficiary', 'editview')
    ->name('mark-beneficiary');
    Route::post('/final-marked', 'marked')
    ->name('final-marked');
    Route::get('/marked-beneficiary-list', 'list')
    ->name('marked-beneficiary-list');
    Route::get('/view-marked-beneficiary-details', 'viewmarkedbeneficiarydetails')
    ->name('view-marked-beneficiary-details');
     Route::post('/marked-beneficiary-details-update', 'updatemarkedbeneficiarydetails')
    ->name('marked-beneficiary-details-update');
});


Route::controller(CreateAssignOtherFormFieldController::class)->group(function () {
    Route::get('/create-dynamicformfield',  'createdynamicformfield')
        ->name('create-dynamicformfield');
});

Route::get(
    '/dynamic-form-page',
    [DynamicFormController::class, 'show']
)->name('dynamic-form-page');

Route::get('/master-tab', App\Livewire\MasterTabManager::class)->name('master-tab');
// Route::get('/tab-filed-manage', App\Livewire\SchemeTabFieldManager::class)->name('tab-filed-manage');
Route::get('/tab-field-manager/{scheme_id?}', SchemeTabFieldManager::class)
    ->name('tab-field-manager');

// Route::get('/menu-tab', App\Livewire\MenuTabManager::class)->name(name: 'menu-tab');
