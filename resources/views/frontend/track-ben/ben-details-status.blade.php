@extends('frontend.layouts.app-template')

@section('content')
    @include('frontend.components.top-header')
    @include('frontend.components.header')

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 lg:py-10">
        <!-- Beneficiary Header Card -->
        <div
            class="bg-white shadow-lg rounded-2xl border border-gray-100 p-5 md:p-7 mb-8 transition-all duration-300 hover:shadow-xl">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">
                <div class="w-full">
                    <!-- Beneficiary Name Section -->
                    <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 flex items-center gap-3">
                            <i class="fa-solid fa-circle-user text-indigo-600 text-3xl md:text-4xl"></i>
                            <span class="bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
                                {{ $benPersonal->beneficiary_name ?? 'Beneficiary Details' }}
                            </span>
                        </h2>

                        <!-- Current Status Badge -->
                        @if(isset($status))
                            <div
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold shadow-sm md:ml-auto whitespace-nowrap {{ $statusClass == 'status-active' ? 'bg-green-100 text-green-700 border border-green-200' : ($statusClass == 'status-rejected' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-yellow-100 text-yellow-700 border border-yellow-200') }}">
                                <span class="relative flex h-3 w-3">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $statusClass == 'status-active' ? 'bg-green-400' : ($statusClass == 'status-rejected' ? 'bg-red-400' : 'bg-yellow-400') }}"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-3 w-3 {{ $statusClass == 'status-active' ? 'bg-green-500' : ($statusClass == 'status-rejected' ? 'bg-red-500' : 'bg-yellow-500') }}"></span>
                                </span>
                                Current Status: {{ $status }}
                            </div>
                        @endif
                    </div>

                    <!-- Scheme Badge -->
                    <div
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-50 to-indigo-100/50 px-4 py-2.5 rounded-xl border border-indigo-200 mb-5 shadow-sm">
                        <i class="fa-solid fa-layer-group text-indigo-600 text-base"></i>
                        <span class="text-sm font-semibold text-gray-700">Scheme Name:</span>
                        <span class="font-bold text-indigo-700 bg-white/80 px-3 py-1 rounded-lg text-sm shadow-sm">
                            {{ $schemename ?? 'N/A' }}
                        </span>
                    </div>

                    <!-- IDs Section -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <!-- Beneficiary ID Card -->
                        <div
                            class="group flex items-center gap-3 bg-gray-50/90 hover:bg-white px-4 py-3 rounded-xl border border-gray-200 hover:border-indigo-300 transition-all duration-200 hover:shadow-md">
                            <div
                                class="w-9 h-9 rounded-full bg-indigo-100 group-hover:bg-indigo-200 flex items-center justify-center transition-colors duration-200">
                                <i class="fa-regular fa-id-card text-indigo-600 text-base"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Beneficiary ID</p>
                                <p class="font-mono font-semibold text-gray-800 text-sm md:text-base break-all">
                                    {{ $benPersonal->beneficiary_id ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <!-- Application ID Card -->
                        <div
                            class="group flex items-center gap-3 bg-gray-50/90 hover:bg-white px-4 py-3 rounded-xl border border-gray-200 hover:border-purple-300 transition-all duration-200 hover:shadow-md">
                            <div
                                class="w-9 h-9 rounded-full bg-purple-100 group-hover:bg-purple-200 flex items-center justify-center transition-colors duration-200">
                                <i class="fa-regular fa-file-lines text-purple-600 text-base"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">Application ID</p>
                                <p class="font-mono font-semibold text-gray-800 text-sm md:text-base break-all">
                                    {{ $benPersonal->application_id ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Header -->
        <div class="mb-8">
            <h2
                class="text-xl md:text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-700 to-indigo-500 text-center mb-2 flex items-center justify-center gap-3">
                <i class="fa-solid fa-list-check text-indigo-600 text-2xl"></i>
                <span>Application Details</span>
            </h2>
            <div class="w-20 h-1 bg-gradient-to-r from-indigo-600 to-indigo-400 mx-auto rounded-full"></div>
        </div>

        <!-- Application Details Tabs -->
        <div class="space-y-4">
            <livewire:application-details.tab-wise-application-view :id="$application_id" :schemeId="$scheme_id"
                :allowedTabCodes="[101]" />
            <livewire:application-details.tab-wise-application-view :id="$application_id" :schemeId="$scheme_id"
                :allowedTabCodes="[102]" />
            <livewire:application-details.tab-wise-application-view :id="$application_id" :schemeId="$scheme_id"
                :allowedTabCodes="[103]" />
            <livewire:application-details.tab-wise-application-view :id="$application_id" :schemeId="$scheme_id"
                :allowedTabCodes="[104]" />
            <livewire:application-details.tab-wise-application-view :id="$application_id" :schemeId="$scheme_id"
                :allowedTabCodes="[105]" />
        </div>

        <!-- Section Header for Activity Log -->
        <div class="mb-8 mt-12">
            <h2
                class="text-xl md:text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-700 to-indigo-500 text-center mb-2 flex items-center justify-center gap-3">
                <i class="fa-solid fa-clock-rotate-left text-indigo-600 text-2xl"></i>
                <span>Activity Log</span>
            </h2>
            <div class="w-20 h-1 bg-gradient-to-r from-indigo-600 to-indigo-400 mx-auto rounded-full"></div>
            <p class="text-center text-sm text-gray-500 mt-2">Track all changes and activities related to this application
            </p>
        </div>

        <!-- Timeline View with Enhanced Features -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            @if(!empty($activityLogData) && count($activityLogData) > 0)
                <!-- Activity Summary Stats -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-4 border-b border-gray-100">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <i class="fa-solid fa-chart-line text-indigo-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Total Activities</p>
                                <p class="text-2xl font-bold text-indigo-700">{{ count($activityLogData) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button id="expandAllLogs"
                                class="text-sm px-3 py-1.5 bg-white border border-gray-200 rounded-lg hover:border-indigo-300 hover:text-indigo-600 transition-all flex items-center gap-2 shadow-sm">
                                <i class="fa-solid fa-expand-alt text-xs"></i>
                                <span>Expand All</span>
                            </button>
                            <button id="collapseAllLogs"
                                class="text-sm px-3 py-1.5 bg-white border border-gray-200 rounded-lg hover:border-indigo-300 hover:text-indigo-600 transition-all flex items-center gap-2 shadow-sm">
                                <i class="fa-solid fa-compress-alt text-xs"></i>
                                <span>Collapse All</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Timeline Container -->
                <div
                    class="relative border-l-2 border-indigo-200 ml-4 md:ml-8 p-6 md:p-8 max-h-[600px] overflow-y-auto custom-scrollbar">
                    <div class="space-y-8">
                        @foreach($activityLogData as $index => $log)
                            <div class="relative group" data-log-id="{{ $index }}">
                                <!-- Timeline Dot with Animation -->
                                <div class="absolute -left-[13px] top-2">
                                    <div class="relative">
                                        <div
                                            class="h-5 w-5 rounded-full bg-indigo-500 ring-4 ring-indigo-100 group-hover:ring-indigo-200 transition-all duration-300 z-10 relative">
                                        </div>
                                        <div
                                            class="absolute top-0 left-0 h-5 w-5 rounded-full bg-indigo-400 animate-ping opacity-20">
                                        </div>
                                    </div>
                                </div>

                                <!-- Log Card -->
                                <div
                                    class="bg-white rounded-xl border border-gray-200 hover:border-indigo-200 shadow-sm hover:shadow-lg transition-all duration-300 ml-3 md:ml-4">
                                    <!-- Card Header (Always Visible) -->
                                    <div class="p-4 md:p-5 cursor-pointer log-header" data-log-id="{{ $index }}">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="flex items-center gap-3 flex-1">
                                                <!-- Icon based on operation type -->
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center shadow-sm">
                                                    @php
                                                        $operationIcon = match (strtolower($log['operation'] ?? '')) {
                                                            'create', 'created' => 'fa-plus',
                                                            'update', 'updated' => 'fa-pen',
                                                            'delete', 'deleted' => 'fa-trash',
                                                            'approve', 'approved' => 'fa-check-circle',
                                                            'reject', 'rejected' => 'fa-times-circle',
                                                            'submit', 'submitted' => 'fa-paper-plane',
                                                            default => 'fa-circle-info'
                                                        };
                                                        $operationColor = match (strtolower($log['operation'] ?? '')) {
                                                            'create', 'created' => 'text-emerald-600',
                                                            'update', 'updated' => 'text-blue-600',
                                                            'delete', 'deleted' => 'text-red-600',
                                                            'approve', 'approved' => 'text-green-600',
                                                            'reject', 'rejected' => 'text-orange-600',
                                                            'submit', 'submitted' => 'text-purple-600',
                                                            default => 'text-indigo-600'
                                                        };
                                                    @endphp
                                                    <i class="fa-solid {{ $operationIcon }} {{ $operationColor }} text-base"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <h3
                                                        class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2 flex-wrap">
                                                        {{ $log['operation'] ?? 'Activity' }}
                                                        @if(isset($log['status']))
                                                            <span
                                                                class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-normal">
                                                                {{ $log['status'] }}
                                                            </span>
                                                        @endif
                                                    </h3>
                                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                                                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-500">
                                                            <i class="fa-regular fa-calendar"></i>
                                                            {{ $log['action_date'] ?? 'Date not specified' }}
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-500">
                                                            <i class="fa-regular fa-clock"></i>
                                                            @php
                                                                $date = isset($log['action_date']) ? \Carbon\Carbon::parse($log['action_date']) : null;
                                                            @endphp
                                                            {{ $date ? $date->format('h:i A') : 'Time not specified' }}
                                                        </span>
                                                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-500">
                                                            <i class="fa-solid fa-user"></i>
                                                            By: <span
                                                                class="font-medium text-gray-700">{{ $log['action_by'] ?? 'System' }}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if(!empty($log['old_data']) || !empty($log['new_data']))
                                                    <span
                                                        class="text-xs text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full flex items-center gap-1">
                                                        <i class="fa-solid fa-code-branch text-xs"></i>
                                                        <span>Changes Available</span>
                                                    </span>
                                                @endif
                                                <i class="fa-solid fa-chevron-down text-gray-400 transition-transform duration-300 log-toggle-icon"
                                                    data-log-id="{{ $index }}"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Collapsible Details Section -->
                                    <div class="log-details hidden border-t border-gray-100 bg-gray-50/50"
                                        data-log-id="{{ $index }}">
                                        <div class="p-4 md:p-5 space-y-4">
                                            <!-- IP Address & Additional Info -->
                                            @if(!empty($log['ip_address']) || !empty($log['user_agent']))
                                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                                        @if(!empty($log['ip_address']))
                                                            <div class="flex items-center gap-2">
                                                                <i class="fa-solid fa-network-wired text-indigo-500 text-xs"></i>
                                                                <span class="text-gray-600">IP Address:</span>
                                                                <code
                                                                    class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $log['ip_address'] }}</code>
                                                            </div>
                                                        @endif
                                                        @if(!empty($log['user_agent']))
                                                            <div class="flex items-center gap-2">
                                                                <i class="fa-solid fa-laptop text-indigo-500 text-xs"></i>
                                                                <span class="text-gray-600">Device:</span>
                                                                <span class="text-xs text-gray-700 truncate">{{ $log['user_agent'] }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Data Changes Comparison -->
                                            @if(!empty($log['old_data']) || !empty($log['new_data']))
                                                <div>
                                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                                        <i class="fa-solid fa-code-compare text-indigo-500"></i>
                                                        Data Changes
                                                    </h4>
                                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                        @if(!empty($log['old_data']))
                                                            <div
                                                                class="bg-white rounded-lg border border-rose-200 overflow-hidden hover:shadow-md transition-shadow">
                                                                <div
                                                                    class="bg-gradient-to-r from-rose-50 to-rose-100/50 px-3 py-2 border-b border-rose-200">
                                                                    <span
                                                                        class="text-xs font-bold text-rose-600 uppercase tracking-wider flex items-center gap-1">
                                                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                                                        Previous Data
                                                                    </span>
                                                                </div>
                                                                <div class="p-3">
                                                                    <pre
                                                                        class="text-xs text-gray-700 font-mono whitespace-pre-wrap break-words bg-rose-50/30 p-2 rounded max-h-48 overflow-y-auto">{{ json_encode(json_decode($log['old_data']), JSON_PRETTY_PRINT) ?: $log['old_data'] }}</pre>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if(!empty($log['new_data']))
                                                            <div
                                                                class="bg-white rounded-lg border border-emerald-200 overflow-hidden hover:shadow-md transition-shadow">
                                                                <div
                                                                    class="bg-gradient-to-r from-emerald-50 to-emerald-100/50 px-3 py-2 border-b border-emerald-200">
                                                                    <span
                                                                        class="text-xs font-bold text-emerald-600 uppercase tracking-wider flex items-center gap-1">
                                                                        <i class="fa-solid fa-bolt"></i>
                                                                        New Data
                                                                    </span>
                                                                </div>
                                                                <div class="p-3">
                                                                    <pre
                                                                        class="text-xs text-gray-700 font-mono whitespace-pre-wrap break-words bg-emerald-50/30 p-2 rounded max-h-48 overflow-y-auto">{{ json_encode(json_decode($log['new_data']), JSON_PRETTY_PRINT) ?: $log['new_data'] }}</pre>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Comments/Notes -->
                                            @if(!empty($log['comment']))
                                                <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                                    <div class="flex items-start gap-2">
                                                        <i class="fa-solid fa-comment-dots text-blue-500 mt-0.5"></i>
                                                        <div class="flex-1">
                                                            <p class="text-xs font-semibold text-blue-700 uppercase mb-1">Remarks /
                                                                Comments</p>
                                                            <p class="text-sm text-gray-700">{{ $log['comment'] }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Load More Button (if needed) -->
                @if(isset($hasMoreLogs) && $hasMoreLogs)
                    <div class="border-t border-gray-100 p-4 text-center">
                        <button id="loadMoreLogs"
                            class="px-6 py-2 bg-white border border-indigo-300 text-indigo-600 rounded-lg hover:bg-indigo-50 transition-all text-sm font-medium shadow-sm">
                            <i class="fa-solid fa-arrow-down mr-2"></i>
                            Load More Activities
                        </button>
                    </div>
                @endif
            @else
                <!-- Empty State with Enhanced Design -->
                <div class="flex flex-col items-center justify-center py-16 px-4">
                    <div class="relative">
                        <div
                            class="w-24 h-24 bg-gradient-to-br from-gray-50 to-gray-100 rounded-full flex items-center justify-center mb-4 border border-gray-200 shadow-inner">
                            <i class="fa-regular fa-clock text-4xl text-gray-400"></i>
                        </div>
                        <div
                            class="absolute -top-2 -right-2 w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center animate-pulse">
                            <i class="fa-solid fa-zzz text-xs text-gray-500"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No Activity Logs Found</h3>
                    <p class="text-sm text-gray-500 text-center max-w-sm">
                        There are no recorded activities for this application yet. Activities will appear here once changes are
                        made.
                    </p>
                    <div class="mt-4 flex items-center gap-2 text-xs text-gray-400">
                        <i class="fa-solid fa-info-circle"></i>
                        <span>Activities include updates, approvals, and status changes</span>
                    </div>
                </div>
            @endif
        </div>


    </div>

    @include('frontend.layouts.footer')
@endsection

@push('styles')
  <style>
                /* Custom Scrollbar */
                .custom-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }

                .custom-scrollbar::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 10px;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #c7d2fe;
                    border-radius: 10px;
                }

                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: #818cf8;
                }

                /* Animation for expand/collapse */
                .log-details {
                    transition: all 0.3s ease-out;
                }

                .log-details:not(.hidden) {
                    display: block;
                    animation: slideDown 0.3s ease-out;
                }

                @keyframes slideDown {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                /* Hover effect improvements */
                .log-header:hover {
                    background: linear-gradient(to right, #f9fafb, #ffffff);
                }

                /* Responsive adjustments */
                @media (max-width: 640px) {
                    .custom-scrollbar {
                        max-height: 450px;
                    }
                }
            </style>
    <style>
        /* Custom animations and improvements */
        .hover\:shadow-xl:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .group:hover .group-hover\:bg-indigo-200 {
            transition: background-color 0.2s ease;
        }

        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }

        /* Better text handling */
        .break-all {
            word-break: break-all;
        }

        /* Responsive improvements */
        @media (max-width: 640px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
@endpush
    @push('scripts')
        <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Toggle individual log details
                    const logHeaders = document.querySelectorAll('.log-header');
                    const toggleIcons = document.querySelectorAll('.log-toggle-icon');

                    function toggleLogDetails(logId) {
                        const detailsDiv = document.querySelector(`.log-details[data-log-id="${logId}"]`);
                        const icon = document.querySelector(`.log-toggle-icon[data-log-id="${logId}"]`);

                        if (detailsDiv) {
                            detailsDiv.classList.toggle('hidden');
                            if (icon) {
                                icon.style.transform = detailsDiv.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
                            }
                        }
                    }

                    logHeaders.forEach(header => {
                        header.addEventListener('click', function (e) {
                            e.stopPropagation();
                            const logId = this.getAttribute('data-log-id');
                            toggleLogDetails(logId);
                        });
                    });

                    toggleIcons.forEach(icon => {
                        icon.addEventListener('click', function (e) {
                            e.stopPropagation();
                            const logId = this.getAttribute('data-log-id');
                            toggleLogDetails(logId);
                        });
                    });

                    // Expand All functionality
                    const expandAllBtn = document.getElementById('expandAllLogs');
                    if (expandAllBtn) {
                        expandAllBtn.addEventListener('click', function () {
                            const allDetails = document.querySelectorAll('.log-details');
                            const allIcons = document.querySelectorAll('.log-toggle-icon');

                            allDetails.forEach(detail => {
                                detail.classList.remove('hidden');
                            });
                            allIcons.forEach(icon => {
                                icon.style.transform = 'rotate(180deg)';
                            });
                        });
                    }

                    // Collapse All functionality
                    const collapseAllBtn = document.getElementById('collapseAllLogs');
                    if (collapseAllBtn) {
                        collapseAllBtn.addEventListener('click', function () {
                            const allDetails = document.querySelectorAll('.log-details');
                            const allIcons = document.querySelectorAll('.log-toggle-icon');

                            allDetails.forEach(detail => {
                                detail.classList.add('hidden');
                            });
                            allIcons.forEach(icon => {
                                icon.style.transform = 'rotate(0deg)';
                            });
                        });
                    }

                    // Load More functionality (if needed)
                    const loadMoreBtn = document.getElementById('loadMoreLogs');
                    if (loadMoreBtn) {
                        loadMoreBtn.addEventListener('click', function () {
                            // Add your AJAX call here to load more logs
                            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Loading...';
                            // Simulate loading
                            setTimeout(() => {
                                this.innerHTML = '<i class="fa-solid fa-arrow-down mr-2"></i>Load More Activities';
                                // Add new logs to the timeline
                            }, 1000);
                        });
                    }

                    // Auto-expand if there are changes (optional)
                    const logsWithChanges = document.querySelectorAll('.log-details');
                    if (logsWithChanges.length === 1) {
                        // If only one log, expand it automatically
                        const singleLogId = logsWithChanges[0].getAttribute('data-log-id');
                        if (singleLogId) {
                            setTimeout(() => toggleLogDetails(singleLogId), 500);
                        }
                    }
                });
        </script>
    @endpush