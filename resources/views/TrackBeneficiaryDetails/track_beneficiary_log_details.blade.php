<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <h1 class="text-2xl font-bold text-indigo-800 dark:text-white px-2 py-2">
                Track Beneficiary Details
            </h1>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold 
             bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 
             border border-blue-200 dark:border-blue-800 shadow-sm
             flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                        clip-rule="evenodd" />
                </svg>
                {{ $schemename ?? 'N/A' }}
            </span>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold 
             bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-400 
             border border-gray-200 dark:border-gray-700 shadow-sm
             flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd" />
                </svg>
                Application ID: {{ $benPersonal->application_id }}
            </span>

        </div>
        <x-form.back-button :url="route('track-beneficiary-details')" />
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 lg:py-10">
            <!-- Beneficiary Header Card -->
            <div
                class="bg-white shadow-lg rounded-2xl border border-gray-100 p-5 md:p-7 mb-8 transition-all duration-300 hover:shadow-xl">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">
                    <div class="w-full">
                        <!-- Beneficiary Name Section -->
                        <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 flex items-center gap-3">
                                @if ($ben_profile_pic && ($ben_profile_pic['document_mime_type'] == 'image/jpeg' || $ben_profile_pic['document_mime_type'] == 'image/png'))
                                @php
                                $document_mime_type = $ben_profile_pic['document_mime_type'];
                                if ($document_mime_type == 'image/jpeg') {
                                $image_extension = 'jpg';
                                } else if ($document_mime_type == 'image/png') {
                                $image_extension = 'png';
                                } else if ($document_mime_type == 'application/pdf') {
                                $image_extension = 'pdf';
                                }
                                $row_image = "data:image/" . $image_extension . ";base64," . $ben_profile_pic['attched_document'];
                                @endphp
                                <img src="{{ $row_image }}" alt="Profile Picture" class="w-20 h-20 rounded-full">
                                @else
                                <i class="fa-solid fa-circle-user text-indigo-600 text-3xl md:text-4xl"></i>
                                @endif
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
                    </div>
                </div>
                <!-- Timeline Container -->
                <div class="relative mt-4 ml-4 mr-4 md:ml-12 pl-8 md:pl-12 pb-8 border-l-2 border-dashed border-indigo-200 dark:border-gray-700">
                    <div class="space-y-10">
                        @foreach($activityLogData as $index => $log)
                        <div class="relative group" data-log-id="{{ $index }}">
                            <!-- Perfectly Centered Pulsing Timeline Marker -->
                            <!-- -left-[33px] centers a 24px dot on a 2px border within this padding context -->
                            <div class="absolute -left-[43px] md:-left-[60px] top-6 z-10">
                                <div class="relative flex items-center justify-center">
                                    <div class="h-6 w-6 rounded-full bg-white dark:bg-gray-800 border-2 border-indigo-500 shadow-sm z-20 flex items-center justify-center group-hover:scale-125 transition-all duration-500">
                                        <div class="h-2 w-2 rounded-full bg-indigo-500"></div>
                                    </div>
                                    <div class="absolute h-10 w-10 rounded-full bg-indigo-400/30 animate-ping"></div>
                                </div>
                            </div>

                            <!-- Log Card -->
                            <div
                                class="bg-white rounded-xl border border-gray-200 hover:border-indigo-200 shadow-sm hover:shadow-lg transition-all duration-300 ml-2 md:ml-2">
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

    </div>
</x-layouts.app>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logHeaders = document.querySelectorAll('.log-header');
        const expandAllBtn = document.getElementById('expandAllLogs');
        const collapseAllBtn = document.getElementById('collapseAllLogs');

        // Individual toggle
        logHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const logId = this.getAttribute('data-log-id');
                const details = document.querySelector(`.log-details[data-log-id="${logId}"]`);
                const icon = document.querySelector(`.log-toggle-icon[data-log-id="${logId}"]`);

                if (details) {
                    details.classList.toggle('hidden');
                    if (icon) {
                        icon.style.transform = details.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
                    }
                }
            });
        });

        // Expand All
        if (expandAllBtn) {
            expandAllBtn.addEventListener('click', function() {
                document.querySelectorAll('.log-details').forEach(details => {
                    details.classList.remove('hidden');
                });
                document.querySelectorAll('.log-toggle-icon').forEach(icon => {
                    icon.style.transform = 'rotate(180deg)';
                });
            });
        }

        // Collapse All
        if (collapseAllBtn) {
            collapseAllBtn.addEventListener('click', function() {
                document.querySelectorAll('.log-details').forEach(details => {
                    details.classList.add('hidden');
                });
                document.querySelectorAll('.log-toggle-icon').forEach(icon => {
                    icon.style.transform = 'rotate(0deg)';
                });
            });
        }
    });
</script>