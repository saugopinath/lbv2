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
use App\Http\Controllers\BeneficiaryCountController;
use App\Http\Controllers\CmoController;
use App\Http\Controllers\JnpmController;
use App\Http\Controllers\RolePermisssionManagementController;
use App\Livewire\OfficeMasters\Create as OfficeMasterCreate;
use App\Http\Controllers\MisReportController;
use Illuminate\Http\Request;

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
        ->middleware('permission.redirect:canRolePermissionManagement')
        ->name('role-permission-management');

    // Duty Management
    Route::get('/userDutymanagement', [UserDutyManagementController::class, 'index'])
        ->middleware('permission.redirect:manage user duties')
        ->name('userDutymanagement.index');

    // LB & Workflow
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
        // ->middleware('permission.redirect:canUpdateIncomplet')
        ->name('incomplete-full-deatils-update');

    Route::post('/incomplete/revert/{id}', [IncompleteTypeController::class, 'revertVerify'])
        // ->middleware('permission.redirect:canRevertIncomplet')
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
        ->middleware('permission.redirect:canEditCaste')
        ->name('caste-modification.edit');

    Route::post('/beneficiary/update-caste', [CasteModificationController::class, 'updateCaste'])
        ->middleware('permission.redirect:canUpdateCaste')
        ->name('beneficiary.updateCaste');

    Route::get('/caste-modification-list', [CasteModificationController::class, 'list'])
        ->middleware('permission.redirect:canCasteModification')
        ->name('caste-modification-list');

    Route::get('/view-beneficiary-details', [CasteModificationController::class, 'viewAppDetails'])
        ->middleware('permission.redirect:canBeneficiaryDetails')
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
    Route::get('/incomplete-details-mis-report', [MisReportController::class, 'incompleteDetails'])
        ->name('incomplete.details.mis.report');

    // Design Pages (Dev Only – Remove in Prod)
    Route::get('/tableDesign', [DesignController::class, 'tableDesign'])->name('tableDesign');
    Route::get('/selectionDesign', [DesignController::class, 'selectionDesign'])->name('selectionDesign');
    Route::get('/viewpage', [DesignController::class, 'viewPage'])->name('viewpage');
    Route::get('/custom_application/{id}', ApplicationView::class)->name('custom_application.view');
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
    Route::get('/reject-approved-beneficiary',  'index')->name('reject-approved-beneficiary');
    Route::get('/reject-approved-beneficiary/de-activate', 'editview')->name('reject-approved-beneficiary.de-activate');
    Route::post('/deActivebeneficiary', 'deActiveBeneficiary')->name('beneficiary.deActivebeneficiary');
});

Route::post('/mis/report/redirect', function (Request $request) {
    // basic validation to ensure a value was submitted
    $request->validate([
        'mis_route' => 'required|url',
    ]);

    return redirect()->to($request->mis_route);
})->name('mis.report.redirect');

//Beneficiary count
Route::controller(BeneficiaryCountController::class)->group(function () {
    Route::get('/beneficiary-reportlist',  'misReport')->name('beneficiary-reportlist');
});

Route::controller(JnpmController::class)->group(function () {

    Route::any('/jnmp/pull', 'pullJnmpData')->middleware('permission.redirect:canImportJanmaMrityuData')->name('jnmp.pull');

    Route::post('/jnmp/details-callback', 'detailsCallback')->name('jnmp.details-callback');

    Route::post('/jnmp/submit', 'submitJnmpData')->name('jnmp.submit');

    Route::get('/jnmp-stats',  'getJnmpStats');
    Route::post('/jnmp/mark-as-death',  'markAsDeathProcess')->name('jnmp.mark-as-death');

    Route::get('jnmp-data', 'index')->middleware('permission.redirect:canReActivateDeathIncident')->name('jnmp-data');

        // JNMP List at HOD
    Route::get('jnmp-marked-data', 'jnmpMarkedDataAtHOD')->name('jnmp-marked-data');
    Route::post('jnmpMarkedData', 'jnmpMarkedData')->name('jnmpMarkedData');
    // Route::post('generateJnmpDataHodExcel', 'generateJnmpDataHodExcel')->name('generateJnmpDataHodExcel');
});
