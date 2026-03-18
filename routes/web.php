<?php

use App\Http\Controllers\Formcontroller;
use App\Http\Controllers\RejectApprovedBeneficiaryController;
use App\Http\Controllers\SchemeController;
use App\Http\Controllers\workflowmanagementController;
use App\Livewire\ApplicationView;
use App\Livewire\IncompletTypePage;
use App\Livewire\SchemeDropdown;
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
use App\Http\Controllers\MasterTabCreationController;
use App\Livewire\OfficeMasters\Create as OfficeMasterCreate;
use App\Http\Controllers\MisReportController;
use App\Http\Controllers\SchemeCapacityController;
use App\Http\Controllers\ValidationManagerController;
use App\Livewire\RolerankManagement;
use App\Livewire\SchemeTabFieldManager;
use App\Livewire\CsvSplitter;

require __DIR__ . '/home.php';


// Guest Routes
Route::get('/session-expired', function () {
    return view('auth.session-expired', [
        'expired_at' => now()->format('h:i:s A')
    ]);
})->name('session.expired');

// Route::get('/', fn() => view('welcome'));
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

    Route::get('lb-application-list', [SchemeController::class, 'finalSubmitted'])
        ->name('lb-application-list');

    Route::get('/lb-application-list/{scheme_id?}', SchemeDropdown::class)
        ->name('lb-application-list');

    Route::get('/application', DraftApplicationView::class)
        ->name('draft-application.view');

    // User Management
    Route::get('/user-managements', [UsersController::class, 'index'])
        ->middleware('permission.redirect:canViewUser')
        ->name('user-managements');

    Route::get('/users/create', UsersCreate::class)
        ->middleware('permission.redirect:canCreateUsers')
        ->name('users');

    // Role & Office Mappings
    Route::get('role-office-master-mappings', [RoleOfficeTypeMappingsController::class, 'index'])
        ->middleware('permission.redirect:canRoleMapping')
        ->name('role-office-master-mappings');

    Route::get('/role-office-type-mappings-create', Create::class)
        ->middleware('permission.redirect:canRoleMappings')
        ->name('role-office-type-mappings-create');

    // Office Masters
    Route::get('officemasters', [OfficeMastersController::class, 'index'])
        ->middleware('permission.redirect:canViewOffices')
        ->name('officemasters');

    Route::get('/office-masters-create', OfficeMasterCreate::class)
        ->middleware('permission.redirect:canCreateOffices')
        ->name('office-masters-create');

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
        // ->middleware('permission.redirect:manage user duties')
        ->name('userDutymanagement.index');


         // Incomplete Types
    Route::get('/incomplete-types/{stage?}', [IncompleteTypeController::class, 'index'])
        ->name('incomplete.types');

    // Route::get('/incomplete-types/{stage?}', [SchemeController::class, 'finalSubmitted'])
    //     ->name('incomplete.types');

    Route::get('/incomplet-type/{id}/{stage}/{schemeId}', IncompletTypePage::class)
        ->name('incomplet-type.view');

    Route::post('/incomplete/update/{id}/{schemeId}', [IncompleteTypeController::class, 'fullUpdate'])
        // ->middleware('permission.redirect:canUpdateIncomplet')
        ->name('incomplete-full-deatils-update');

    Route::post('/incomplete/revert/{id}/{schemeId}', [IncompleteTypeController::class, 'revertVerify'])
        // ->middleware('permission.redirect:canRevertIncomplet')
        ->name('incomplete-revert-update');
    // Route::get('/incomplete-details-mis-report', [IncompleteTypeController::class, 'incompleteDetails'])
    //     ->name('incomplete.details.mis.report');


    // Design Pages (Dev Only – Remove in Prod)
    Route::get('/tableDesign', [DesignController::class, 'tableDesign'])->name('tableDesign');
    Route::get('/selectionDesign', [DesignController::class, 'selectionDesign'])->name('selectionDesign');
    Route::get('/viewpage', [DesignController::class, 'viewPage'])->name('viewpage');
    // Route::get('/custom_application/{id}', ApplicationView::class)->name('custom_application.view');
    Route::get('/getelsticsearchIndex', [ElasticSearchController::class, 'index'])->name('getelsticsearchIndex');
});

Route::controller(CreateAssignOtherFormFieldController::class)->group(function () {
    Route::get('/create-dynamicformfield', 'createdynamicformfield')
        ->name('create-dynamicformfield');
});

Route::get(
    '/dynamic-form-page',
    [DynamicFormController::class, 'show']
)->name('dynamic-form-page');

Route::get('/master-tab', App\Livewire\MasterTabManager::class)
    ->middleware('permission.redirect:canMasterTab')
    ->name('master-tab');
// Route::get('/tab-filed-manage', App\Livewire\SchemeTabFieldManager::class)->name('tab-filed-manage');
Route::get('/tab-field-manager', SchemeTabFieldManager::class)->name('tab-field-manager');
// Route::get('/menu-tab', App\Livewire\MenuTabManager::class)->name(name: 'menu-tab');

Route::get('/edit-validation', [ValidationManagerController::class, 'index'])->name('edit-validation');
Route::get('/master-tab-creation', [MasterTabCreationController::class, 'index'])->name('master-tab-creation');

Route::get('/schemes-final-submitted', [SchemeController::class, 'finalSubmitted'])
    ->middleware('permission.redirect:canEntry')
    ->name('schemes.final-submitted');

Route::get('/duplicate-checks', [SchemeController::class, 'finalSubmitted'])->name('duplicate-checks');
Route::get('/age-management', [SchemeController::class, 'finalSubmitted'])->name('age-management');

Route::controller(workflowmanagementController::class)->group(function () {
    Route::any('/create-steps', 'createSteps')->name('create-steps');
    Route::any('/assign-workflow', 'assignWorkflow')->name('assign-workflow');
});

Route::get('/role-rank-management', RolerankManagement::class)
    ->middleware('permission.redirect:canRoleRankManagement')
    ->name('role-rank-management');

Route::get('/define-workflow', [SchemeController::class, 'finalSubmitted'])
    ->middleware('permission.redirect:canDefineWorkflow')
    ->name('define-workflow');

Route::get('/beneficiaries_selection', [BeneficiaryListController::class, 'index'])
    ->name('beneficiaries_selection.index');

Route::get('/report', [BeneficiaryListController::class, 'show'])
    ->name('report.show');

Route::any('draftedit', [SchemeController::class, 'draftedit'])
    ->name('draftedit');

Route::any('/custom_application', [SchemeController::class, 'applicationView'])->name('custom_application.view');

// Caste Update
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
    // ->middleware('permission.redirect:canCasteModification')
    ->name('caste-modification-list');

Route::get('/view-beneficiary-details', [CasteModificationController::class, 'viewAppDetails'])
    // ->middleware('permission.redirect:canBeneficiaryDetails')
    ->name('view-beneficiary-details');

Route::get('/scheme-capacity', [SchemeCapacityController::class, 'index'])
    ->name('scheme-capacity');

Route::get('/csv-splitter', CsvSplitter::class)
    ->name('csv-splitter');


Route::get('/form', [Formcontroller::class, 'index'])
    ->name('form');
Route::get('application-lists', [Formcontroller::class, 'applicationLists'])
    ->name('application-lists');
Route::get('/define-workflow1', [workflowmanagementController::class, 'index'])
    ->name('define-workflow1');

//Reject Approved Beneficiary
Route::controller(RejectApprovedBeneficiaryController::class)->group(function () {
    Route::get('/reject-approved-beneficiary',  'index')
        // ->middleware('permission.redirect:canRejectApprovedBeneficiary')
        ->name('reject-approved-beneficiary');
    Route::get('/reject-approved-beneficiary/de-activate', 'editview')
        // ->middleware('permission.redirect:canViewDetailsToReject')
        ->name('reject-approved-beneficiary.de-activate');
    Route::post('/deActivebeneficiary', 'deActiveBeneficiary')
        // ->middleware('permission.redirect:canRejectBeneficiary')
        ->name('beneficiary.deActivebeneficiary');
});
