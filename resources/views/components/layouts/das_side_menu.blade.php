@php
    $user = auth()->user();
    $stage = request()->route('stage');

    $currentActiveMenu = null;
    if (request()->routeIs(['form*', 'application-lists*', 'test.duty*'])) {
        $currentActiveMenu = 'LBFrom';
    } elseif (request()->routeIs(['duty-assignment-role-office-master-mappings*', 'officemasters*', 'user-managements*'])) {
        $currentActiveMenu = 'DutyAssignment';
    } elseif (request()->routeIs(['role-office-master-mappings*'])) {
        $currentActiveMenu = 'DutyManagement';
    } elseif (request()->routeIs(['incomplete.types*'])) {
        $currentActiveMenu = 'Incomplete';
    } elseif (request()->routeIs(['schemes*', 'master-tab*', 'define-workflow1*', 'configured-workflows*', 'scheme-capacity*'])) {
        $currentActiveMenu = 'SchemeOnboard';
    } elseif (request()->routeIs(['role-permission-management*', 'permission-information*']) || (request()->routeIs(['role-rank-management*', 'permission*', 'user-permission*']) && !request()->routeIs('duty*'))) {
        $currentActiveMenu = 'CreatePermission';
    } elseif (request()->routeIs(['caste-management*', 'caste-management-request-list*', 'update-caste-management-details*'])) {
        $currentActiveMenu = 'CasteManagement';
    } elseif (request()->routeIs(['cmo-grievance-workflow*'])) {
        $currentActiveMenu = 'Sarasori Mukhyamantri';
    }
@endphp

<aside :class="sidebar ? 'w-60' : 'w-16'" class="transition-all duration-300 bg-gradient-to-r from-cyan-800 to-cyan-600 dark:bg-gray-800 shadow-lg flex flex-col h-screen" x-data="{
    openMenus: {{ json_encode($currentActiveMenu ? [$currentActiveMenu] : []) }},
    activeRouteMenu: '{{ $currentActiveMenu }}',
    allMenuKeys: ['LBFrom', 'DutyManagement', 'DutyAssignment', 'Incomplete', 'SchemeOnboard', 'CreatePermission', 'CasteManagement', 'Sarasori Mukhyamantri'],
    isOpen(menu) {
        return this.openMenus.includes(menu);
    },
    toggle(menu) {
        if (this.isOpen(menu)) {
            this.openMenus = this.openMenus.filter(m => m !== menu);
        } else {
            this.openMenus.push(menu);
        }
    },
    isAllExpanded() {
        return this.allMenuKeys.length > 0 && this.allMenuKeys.every(k => this.openMenus.includes(k));
    },
    toggleAll() {
        if (this.isAllExpanded()) {
            this.collapseAll();
        } else {
            this.expandAll();
        }
    },
    expandAll() {
        this.openMenus = [...this.allMenuKeys];
    },
    collapseAll() {
        this.openMenus = this.activeRouteMenu ? [this.activeRouteMenu] : [];
    }
}">
    <!-- Logo -->
    <div class="flex flex-col items-center border-b border-gray-700 dark:border-gray-700 bg-white {{ config('jblbConf.das_logo_class') }}">
        <img alt="Lakshmir Bhandar" class="{{ config('jblbConf.logo_das_width') }}" src="{{ asset('images/' . config('jblbConf.das_logo')) }}" />
        @if (config('jblbConf.is_lb'))
            <template x-if="sidebar">
                <div class="text-center font-bold text-sm text-blue-600">{{ config('jblbConf.headLine') }}</div>
            </template>
        @endif
    </div>
    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto mt-2 space-y-1 text-sm">
        <!-- Menu Item: Dashboard -->
        <div class="relative flex items-center">
            <a class="flex items-center flex-1 px-4 py-2 text-left text-slate-200 hover:text-white hover:bg-slate-700 dark:hover:bg-slate-700 rounded {{ request()->routeIs('dashboard*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('dashboard') }}">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" fill="currentColor" opacity="0.3" />
                    <path d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z" fill="currentColor" />
                </svg>
                <span class="mr-2 truncate" x-show="sidebar">Dashboard</span>
            </a>
            <button :title="isAllExpanded() ? 'Collapse all menus' : 'Expand all menus'" @click="toggleAll()" aria-label="Toggle all menus" class="absolute right-2 p-1.5 rounded text-slate-200 hover:text-white hover:bg-slate-700 dark:hover:bg-slate-700 transition-colors flex items-center justify-center" type="button" x-show="sidebar">
                <!-- Fold / Collapse All Icon (when expanded) -->
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="isAllExpanded()" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 4l5 5 5-5M7 20l5-5 5 5" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
                <!-- Unfold / Expand All Icon (when collapsed) -->
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!isAllExpanded()" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 15l5 5 5-5M7 9l5-5 5 5" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
            </button>
        </div>

        @if (\App\Helpers\WorkFlowPermissionHelper::canAnyLbMenu() || \App\Helpers\DutyWorkFlowPermissionHelper::canAnyDutyMenu())
            <div>
                <button @click="toggle('LBFrom')" class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" opacity="0.3" />
                        <path d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z" fill="currentColor" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">ANNAPURNA BHANDAR</span>
                    <svg :class="isOpen('LBFrom') ? 'rotate-180' : ''" aria-hidden="true" class="mr-2 w-3 h-3 transition-transform duration-200" fill="none" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
                        <path d="m1 1 4 4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" />
                    </svg>
                </button>

                <!-- Sub-menu -->
                <div class="pl-4" id="list_menu" x-collapse x-show="isOpen('LBFrom')" x-transition>
                    <ul>
                        @if (\App\Helpers\WorkFlowPermissionHelper::canEntry())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('form*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('form') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3" />
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor" />
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Application Form</span>
                                </a>
                            </li>
                        @endif

                        @if (\App\Helpers\WorkFlowPermissionHelper::canViewLbApplications())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('application-lists*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('application-lists') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3" />
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor" />
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Process Application</span>
                                </a>
                            </li>
                        @endif

                        {{-- New Duty-Based Route 1: Application Form (Operator) --}}
                        @if (\App\Helpers\DutyWorkFlowPermissionHelper::canFormEntry())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('form*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('form') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3" />
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor" />
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Application Form</span>
                                </a>
                            </li>
                        @endif

                        {{-- New Duty-Based Route 2: Process Application (Verifier & Approver) --}}
                        @if (\App\Helpers\DutyWorkFlowPermissionHelper::canViewApplications())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('application-lists*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('application-lists') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3" />
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor" />
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Process Application</span>
                                </a>
                            </li>
                        @endif

                        {{-- New Duty-Based Route 3: Test Application (HOD) --}}
                        @if (\App\Helpers\DutyWorkFlowPermissionHelper::canViewApplicationsHod())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('test.duty*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('test.duty') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3" />
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor" />
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Test Application</span>
                                </a>
                            </li>
                        @endif

                        {{-- OLD DYNAMIC MODULE LOOP COMMENTED FOR BACKWARD COMPATIBILITY:
                        @foreach (\App\Helpers\WorkFlowPermissionHelper::getUserModules() as $module)
                            <li class="px-4 py-2 mt-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                {{ $module->module_name }}
                            </li>
                            @if (\App\Helpers\WorkFlowPermissionHelper::canEntry())
                                <li>
                                    <a class="flex item-center px-2 py-1 text-left text-slate-200 rounder hover:bg-slate-700 hover:text-white" href="{{ route('form', ['module' => $module->encrypted_code ?? null]) }}">
                                        <span class="truncate" x-show="sidebar">Application Form</span>
                                    </a>
                                </li>
                            @endif
                            @if (\App\Helpers\WorkFlowPermissionHelper::canViewLbApplications())
                                <li>
                                    <a class="flex item-center px-2 py-1 text-left text-slate-200 rounder hover:bg-slate-700 hover:text-white" href="{{ route('application-lists', ['module' => $module->encrypted_code ?? null]) }}">
                                        <span class="truncate" x-show="sidebar">Process Application</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                        --}}

                    </ul>
                </div>
            </div>
        @endif


        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canDutyManagement())
            {{-- <div>
                <button @click="toggle('DutyManagement')" class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" opacity="0.3" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Duty Management</span>
                    <svg :class="isOpen('DutyManagement') ? 'rotate-180' : ''" aria-hidden="true" class="mr-2 w-3 h-3 transition-transform duration-200" fill="none" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
                        <path d="m1 1 4 4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" />
                    </svg>
                </button>


                <div class="pl-4" id="list_menu" x-collapse x-show="isOpen('DutyManagement')" x-transition>
                    <ul>

                        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canRoleMapping())

                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white" href="{{ route('role-office-master-mappings') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Role Office Type Mapping</span>
                                </a>
                            </li>

                        @endif

                        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canViewOffices())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white" href="{{ route('officemasters') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Office Masters</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canViewUser())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white" href="{{ route('user-managements') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Users</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div> --}}
            <!-- Duty Assignment -->
            <div>
                <button @click="toggle('DutyAssignment')" class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded">

                    <!-- Same icon as Duty Management -->
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" opacity="0.3" />
                    </svg>

                    <span class="mr-2 truncate" x-show="sidebar">
                        Duty Assignment
                    </span>

                    <!-- Same dropdown arrow -->
                    <svg :class="isOpen('DutyAssignment') ? 'rotate-180' : ''" aria-hidden="true" class="mr-2 w-3 h-3 transition-transform duration-200" fill="none" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
                        <path d="m1 1 4 4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" />
                    </svg>
                </button>

                <!-- Duty Assignment Sub Menu -->
                <div class="pl-4" id="duty_assignment_menu" x-collapse x-show="isOpen('DutyAssignment')" x-transition>

                    <ul>
                        <!-- Role Office Type Mapping -->
                        <li>
                            <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('duty-assignment-role-office-master-mappings*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('duty-assignment-role-office-master-mappings') }}">

                                <!-- Same logo as existing submenu items -->
                                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6005 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3">
                                    </path>
                                </svg>

                                <span class="truncate" x-show="sidebar">
                                    Role Office Type Mapping
                                </span>
                            </a>
                        </li>
                        <!-- Office Masters -->
                        <li>
                            <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('officemasters*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('officemasters') }}">

                                <!-- Same logo as existing submenu items -->
                                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6005 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3">
                                    </path>
                                </svg>

                                <span class="truncate" x-show="sidebar">
                                    Office Masters
                                </span>
                            </a>
                        </li>
                        {{--
        <!-- Role Rank Management -->
        <li>
            <a href="{{ route('role-rank-management') }}"
                class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white">

                <!-- Same logo as existing submenu items -->
                <svg class="w-5 h-5 mr-2 flex-shrink-0"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path opacity="0.3"
                        d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6005 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z"
                        fill="currentColor">
                    </path>
                </svg>

                <span x-show="sidebar" class="truncate">
                    Role Rank Management
                </span>
            </a>
        </li>
        
         <!-- Role Office Type Mapping -->
        <li>
            <a href="{{ route('duty-assignment-role-office-master-mappings') }}"
                class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white">

                <!-- Same logo as existing submenu items -->
                <svg class="w-5 h-5 mr-2 flex-shrink-0"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path opacity="0.3"
                        d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6005 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z"
                        fill="currentColor">
                    </path>
                </svg>

                <span x-show="sidebar" class="truncate">
                    Role Office Type Mapping
                </span>
            </a>
        </li>
        
        <!-- Office Masters -->
        <li>
            <a href="{{ route('officemasters') }}"
                class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white">

                <!-- Same logo as existing submenu items -->
                <svg class="w-5 h-5 mr-2 flex-shrink-0"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path opacity="0.3"
                        d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6005 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z"
                        fill="currentColor">
                    </path>
                </svg>

                <span x-show="sidebar" class="truncate">
                    Office Masters
                </span>
            </a>
        </li>
        --}}

                        <!-- Users -->
                        <li>
                            <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('user-managements*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('user-managements') }}">

                                <!-- Same logo as existing submenu items -->
                                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6005 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3">
                                    </path>
                                </svg>

                                <span class="truncate" x-show="sidebar">
                                    Users
                                </span>
                            </a>
                        </li>


                    </ul>
                </div>
            </div>
        @endif

        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canDynamicWorkflowManagement())
            <div>
                <a class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded {{ request()->routeIs('dynamic-workflow-config*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('dynamic-workflow-config') }}">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <path d="M17 20h5v-2a2 2 0 00-2-2h-3m-2-2H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v7m-7 4v5m-7-5h12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Dynamic Workflow Management</span>
                </a>
            </div>
        @endif

        <!-- Update Bank Details -->
        @if (\App\Helpers\WorkFlowPermissionHelper::canUpdateBankDetailsPermission())
            <div>
                <a class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded {{ request()->routeIs('bankUpdate*') || request()->routeIs('bank-update*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('bankUpdate') }}">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Update Bank Details</span>
                </a>
            </div>
        @endif
        @if (\App\Helpers\WorkFlowPermissionHelper::canIncomplete())
            <div>
                <button @click="toggle('Incomplete')" class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 text-slate-200 hover:text-white rounded">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" opacity="0.3" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Incomplete</span>
                    <svg :class="isOpen('Incomplete') ? 'rotate-180' : ''" class="mr-2 w-3 h-3 transition-transform duration-200" fill="none" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
                        <path d="m1 1 4 4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" />
                    </svg>
                </button>

                <!-- Sub-menu -->
                <div class="pl-4" id="list_menu" x-collapse x-show="isOpen('Incomplete')" x-transition>
                    <ul>
                        @if (\App\Helpers\WorkFlowPermissionHelper::canVerifierIncomplete())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('incomplete.types*') && request()->route('type') === 'verifier' ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('incomplete.types', 'verifier') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Verifier Incomplete</span>
                                </a>
                            </li>
                        @endif

                        @if (\App\Helpers\WorkFlowPermissionHelper::canApproverIncomplete())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('incomplete.types*') && request()->route('type') === 'approver' ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('incomplete.types', 'approver') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Approver Incomplete</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif

        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canSchemeOnboard())
            <div>
                <button @click="toggle('SchemeOnboard')" class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" fill="currentColor" opacity="0.3" />
                        <path d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z" fill="currentColor" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Scheme Onboard </span>
                    <svg :class="isOpen('SchemeOnboard') ? 'rotate-180' : ''" aria-hidden="true" class="mr-2 w-3 h-3 transition-transform duration-200" fill="none" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
                        <path d="m1 1 4 4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" />
                    </svg>
                </button>
                <!-- Sub-menu -->

                <div class="pl-4" id="list_menu" x-collapse x-show="isOpen('SchemeOnboard')" x-transition>
                    <ul>
                        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canSchemeOnboard())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('schemes*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('schemes') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Schemes</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canMasterTab())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('master-tab*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('master-tab') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg><span class="truncate" svg="truncate" x-show="sidebar">Form
                                        Management</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canDefineWorkflow())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('define-workflow1*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('define-workflow1') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg><span class="truncate" svg="truncate" x-show="sidebar">Define
                                        Workflow</span></a>
                            </li>
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('configured-workflows*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('configured-workflows') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg><span class="truncate" x-show="sidebar">Configured Workflows</span></a>
                            </li>
                        @endif
                        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canSchemeCapacitySetting())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('scheme-capacity*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('scheme-capacity') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg><span class="truncate" svg="truncate" x-show="sidebar">Scheme Capacity
                                        Setting</span></a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif
        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canUserPermission())
            <div>
                <button @click="toggle('CreatePermission')" class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" fill="currentColor" opacity="0.3" />
                        <path d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z" fill="currentColor" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Role & Permission</span>
                    <svg :class="isOpen('CreatePermission') ? 'rotate-180' : ''" aria-hidden="true" class="mr-2 w-3 h-3 transition-transform duration-200" fill="none" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
                        <path d="m1 1 4 4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" />
                    </svg>
                </button>
                <!-- Sub-menu -->

                <div class="pl-4" id="list_menu" x-collapse x-show="isOpen('CreatePermission')" x-transition>
                    <ul>
                        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canRolePermissionManagement())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('role-permission-management*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('role-permission-management') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg><span class="truncate" svg="truncate" x-show="sidebar">Role
                                        Management</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canRoleRankManagement())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('role-rank-management*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('role-rank-management') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg><span class="truncate" svg="truncate" x-show="sidebar">Role Rank
                                        Management</span></a>
                            </li>
                        @endif
                        {{-- @can('view permission') --}}
                        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canViewPermission())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('permission') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('permission') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg><span class="truncate" svg="truncate" x-show="sidebar">Create
                                        Permission</span></a>
                            </li>
                            {{-- @endcan --}}
                        @endif
                        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canViewUserPermisson())
                            {{-- @can('view user permission') --}}
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('user-permission*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('user-permission') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg><span class="truncate" svg="truncate" x-show="sidebar">Assign
                                        Permission</span></a>
                            </li>
                            {{-- @endcan --}}
                        @endif
                        @if (auth()->user()->mappedRoles->contains('name', 'Super Admin') || \App\Helpers\WorkFlowPermissionHelper::canViewPermission())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('permission-information*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('permission-information') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg><span class="truncate" svg="truncate" x-show="sidebar">Permission Information</span>
                                </a>
                            </li>
                        @endif

                    </ul>
                </div>
            </div>

        @endif
        @if (\App\Helpers\WorkFlowPermissionHelper::canViewBeneficiaries())
            <div>
                <a class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded {{ request()->routeIs('beneficiaries_selection*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('beneficiaries_selection.index') }}">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Beneficiary List</span>
                </a>
            </div>
        @endif
        @if (\App\Helpers\WorkFlowPermissionHelper::canCaste())
            <div>
                <button @click="toggle('CasteManagement')" class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 text-slate-200 hover:text-white rounded">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" opacity="0.3" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Caste Management</span>
                    <svg :class="isOpen('CasteManagement') ? 'rotate-180' : ''" class="mr-2 w-3 h-3 transition-transform duration-200" fill="none" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
                        <path d="m1 1 4 4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" />
                    </svg>
                </button>

                <!-- Sub-menu -->
                <div class="pl-4" id="list_menu" x-collapse x-show="isOpen('CasteManagement')" x-transition>
                    <ul>
                        {{-- @can('modify caste') --}}
                        @if (\App\Helpers\WorkFlowPermissionHelper::canModifyCaste())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('caste-management') || (request()->routeIs('caste-management*') && !request()->routeIs('caste-management-request-list*')) ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('caste-management') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Change Caste</span>
                                </a>
                            </li>
                            {{-- @endcan --}}
                        @endif

                        {{-- @can('view caste modification list') --}}
                        @if (\App\Helpers\WorkFlowPermissionHelper::canCasteModification())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('caste-management-request-list*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('caste-management-request-list') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Report List</span>
                                </a>
                            </li>
                            {{-- @endcan --}}
                        @endif
                        @if (\App\Helpers\WorkFlowPermissionHelper::canModifyCaste())
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('update-caste-management-details*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('update-caste-management-details') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg>
                                    <span class="truncate" x-show="sidebar">Process Caste Application</span>
                                </a>
                            </li>
                            {{-- @endcan --}}
                        @endif


                    </ul>
                </div>
            </div>
            {{-- @endcanany --}}
        @endif

        @if (\App\Helpers\WorkFlowPermissionHelper::canRejectApprovedBeneficiary())
            <div>
                <a class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded {{ request()->routeIs('reject-approved-beneficiary*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('reject-approved-beneficiary') }}">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" fill="currentColor" opacity="0.3" />
                        <path d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z" fill="currentColor" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Reject Approved Beneficiary</span>
                </a>
            </div>
            {{-- @endcan --}}
        @endif
        {{-- @if ($user->hasAnyRole(['Super Admin'])) --}}
        @if (\App\Helpers\WorkFlowPermissionHelper::canImportJanmaMrityuData())
            <div>
                <a class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded {{ request()->routeIs('jnmp.pull*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('jnmp.pull') }}">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" fill="currentColor" opacity="0.3" />
                        <path d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z" fill="currentColor" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Import Janma-Mrityu Data</span>
                </a>
            </div>
        @endif
        {{-- @if ($user->hasAnyRole(['Approver', 'Delegated Approver'])) --}}
        @if (\App\Helpers\WorkFlowPermissionHelper::canReActivateDeathIncident())
            <div>
                <a class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded {{ request()->routeIs('jnmp-data*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('jnmp-data') }}">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" fill="currentColor" opacity="0.3" />
                        <path d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z" fill="currentColor" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Re-activate Death Incident</span>
                </a>
            </div>
        @endif
        @if (\App\Helpers\WorkFlowPermissionHelper::canJanmyaMrityuBeneficiaryList())
            <div>
                <a class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded {{ request()->routeIs('jnmp-marked-data*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('jnmp-marked-data') }}">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" fill="currentColor" opacity="0.3" />
                        <path d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z" fill="currentColor" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Janmya-Mrityu Beneficiary List</span>
                </a>
            </div>
        @endif
        @if (\App\Helpers\WorkFlowPermissionHelper::canCMODataFetch())
            <div>
                <a class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded {{ request()->routeIs('pullnewcmo*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('pullnewcmo') }}">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" fill="currentColor" opacity="0.3" />
                        <path d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z" fill="currentColor" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">CMO Data Fetch</span>
                </a>
            </div>
        @endif
        @if (\App\Helpers\WorkFlowPermissionHelper::canSarasoriMukhyamantri())
            <div>
                <button @click="toggle('Sarasori Mukhyamantri')" class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" fill="currentColor" opacity="0.3" />
                        <path d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z" fill="currentColor" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Sarasori Mukhyamantri</span>
                    <svg :class="isOpen('Sarasori Mukhyamantri') ? 'rotate-180' : ''" aria-hidden="true" class="mr-2 w-3 h-3 transition-transform duration-200" fill="none" viewBox="0 0 10 6" xmlns="http://www.w3.org/2000/svg">
                        <path d="m1 1 4 4 4-4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" />
                    </svg>
                </button>
                <!-- Sub-menu -->
                @if (\App\Helpers\WorkFlowPermissionHelper::canCMOGrievanceMark())
                    <div class="pl-4" id="list_menu" x-collapse x-show="isOpen('Sarasori Mukhyamantri')" x-transition>
                        <ul>
                            <li>
                                <a class="flex item-center px-2 py-1 text-left text-slate-200 rounded hover:bg-slate-700 hover:text-white {{ request()->routeIs('cmo-grievance-workflow*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('cmo-grievance-workflow') }}">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" opacity="0.3"></path>
                                        <path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor"></path>
                                    </svg><span class="truncate" svg="truncate" x-show="sidebar">CMO Grievance
                                        Mark</span></a>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
        @endif
        @if (\App\Helpers\WorkFlowPermissionHelper::canBackFromJb())
            <div>
                <a class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded {{ request()->routeIs('backfromjb*') ? 'bg-slate-700 text-white font-medium' : '' }}" href="{{ route('backfromjb') }}">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" fill="currentColor" opacity="0.3" />
                        <path d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z" fill="currentColor" />
                    </svg>
                    <span class="mr-2 truncate" x-show="sidebar">Back From JB</span>
                </a>
            </div>
        @endif
    </nav>
</aside>
