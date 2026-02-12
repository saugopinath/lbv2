@extends('frontend.layouts.app-template')
@push('styles')
    <style>
        .scroll-container {
            scrollbar-width: thin;
            scrollbar-color: #c7d2fe #f1f5f9;
        }

        .scroll-container::-webkit-scrollbar {
            width: 8px;
        }

        .scroll-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .scroll-container::-webkit-scrollbar-thumb {
            background: #c7d2fe;
            border-radius: 10px;
        }

        .scroll-container::-webkit-scrollbar-thumb:hover {
            background: #a5b4fc;
        }

        .perspective {
            perspective: 1000px;
        }

        .card-inner {
            transform-style: preserve-3d;
        }

        .card-front,
        .card-back {
            backface-visibility: hidden;
        }

        .card-back {
            transform: rotateY(180deg);
        }

        /* Hover effect */
        .card-inner:hover {
            transform: rotateY(180deg);
        }

        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .footer-decoration {
            position: relative;
            overflow: hidden;
        }

        .footer-decoration::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b, #ec4899, #8b5cf6, #10b981);
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .floating-icon {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .icon-1 {
            top: 20%;
            left: 5%;
            animation-delay: 0s;
        }

        .icon-2 {
            top: 60%;
            left: 10%;
            animation-delay: 1s;
        }

        .icon-3 {
            top: 30%;
            right: 5%;
            animation-delay: 2s;
        }

        .icon-4 {
            top: 70%;
            right: 10%;
            animation-delay: 3s;
        }

        .link-hover-effect {
            position: relative;
            transition: all 0.3s ease;
        }

        .link-hover-effect::after {
            content: "";
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #f59e0b;
            transition: width 0.3s ease;
        }

        .link-hover-effect:hover::after {
            width: 100%;
        }

        .contact-icon {
            display: inline-block;
            width: 24px;
            text-align: center;
            margin-right: 8px;
            color: #f59e0b;
        }

        .copyright {
            position: relative;
        }

        .copyright::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 1px;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.3),
                    transparent);
        }
    </style>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.min.css">

@endpush
@section('content')
    <!-- Main Header -->
    @include('frontend.components.top-header')

    <!-- Main Header -->
    @include('frontend.components.header')

    <!-- Notification Section -->
    <section id="notifications" class="max-w-7xl mx-auto px-4 py-12">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Section Header -->
            <div class="bg-indigo-700 text-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-bell text-xl mr-3"></i>
                        <h2 class="text-2xl font-bold">Notifications</h2>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm bg-indigo-800 px-3 py-1 rounded-full">Latest Updates</span>
                        <button class="bg-indigo-800 hover:bg-indigo-900 px-3 py-1 rounded-lg text-sm transition-colors">
                            <i class="fas fa-cog mr-1"></i> Settings
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notification Filters -->
            <div class="bg-gray-50 px-6 py-3 border-b">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700 font-medium">Filter by:</span>
                        <div class="flex flex-wrap gap-2">
                            <button class="filter-btn bg-indigo-100 px-3 py-1 rounded-full" data-type="all">All</button>

                            <button class="filter-btn bg-gray-200 px-3 py-1 rounded-full"
                                data-type="important">Important</button>

                            <button class="filter-btn bg-gray-200 px-3 py-1 rounded-full" data-type="scheme_update">Scheme
                                Updates</button>

                            <button class="filter-btn bg-gray-200 px-3 py-1 rounded-full"
                                data-type="application_status">Application Status</button>

                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="relative">
                            <input id="notificationSearch" type="text" placeholder="Search notifications..."
                                class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">

                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <!-- Notifications List -->
            <div id="notificationList" class="max-h-[500px] overflow-y-auto scroll-container"></div>

            <table id="hiddenNotificationTable" class="hidden">
                <thead>
                    <tr>
                        <th>ID</th>
                    </tr>
                </thead>
            </table>



            <!-- Notifications Footer -->
            <div class="bg-gray-50 px-6 py-3 border-t">
                <div class="flex items-center justify-between">
                    <div id="dtInfo" class="text-sm text-gray-600">
                        Showing 0 of 0 notifications
                    </div>
                    <div class="flex items-center space-x-2">
                        <button id="dtPrev"
                            class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 transition-colors">
                            <i class="fas fa-chevron-left mr-1"></i> Previous
                        </button>
                        <button id="dtNext"
                            class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 transition-colors">
                            Next <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!--Footer-->
    @include('frontend.layouts.footer')
@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.5/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function () {


            let selectedType = 'all';

            const table = $('#hiddenNotificationTable').DataTable({
                processing: true,
                serverSide: true,
                paging: true,
                searching: true,
                lengthChange: false,
                info: false,
                ordering: false,
                pageLength: 6, // MATCH YOUR UI

                dom: 't', // 🔥 HIDE DATATABLE UI COMPLETELY

                ajax: {
                    url: "{{ route('notifications.datatable') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.type = selectedType;
                    },
                    dataSrc: function (json) {
                        renderNotifications(json.data);
                        updateFooter(json);
                        return json.data;
                    }
                },

                columns: [{ data: 'id' }]
            });

            /* =====================
               SEARCH (CUSTOM UI)
            ===================== */
            $('#notificationSearch').on('keyup', function () {
                table.search(this.value).draw();
            });

            /* =====================
               FILTER BUTTONS
            ===================== */
            $('.filter-btn').on('click', function () {
                $('.filter-btn')
                    .removeClass('bg-indigo-100')
                    .addClass('bg-gray-200');

                $(this)
                    .removeClass('bg-gray-200')
                    .addClass('bg-indigo-100');

                selectedType = $(this).data('type');
                table.ajax.reload();
            });

            /* =====================
               PAGINATION BUTTONS
            ===================== */
            $('#dtPrev').on('click', function () {
                table.page('previous').draw('page');
            });

            $('#dtNext').on('click', function () {
                table.page('next').draw('page');
            });

            /* =====================
               RENDER UI
            ===================== */
            function renderNotifications(data) {
                let html = '';

                if (!data.length) {
                    $('#notificationList').html(`
                            <div class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-bell-slash text-3xl mb-2"></i>
                                <p>No notifications found</p>
                            </div>
                        `);
                    return;
                }

                data.forEach(n => {
                    html += `
                        <div class="border-b border-gray-200 hover:bg-green-50 transition-colors">
                            <div class="px-6 py-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-3 h-3 rounded-full ${dotColor(n.type)}"></div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <h3 class="font-semibold text-gray-900">
                                                ${badge(n.type)} ${escape(n.title)}
                                            </h3>
                                            <span class="text-sm text-gray-500">
                                                ${moment(n.notified_at).fromNow()}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-gray-600">${escape(n.message)}</p>
                                        ${n.scheme_name ? `
                                            <div class="mt-2 text-sm text-green-600 font-medium">
                                                ${escape(n.scheme_name)}
                                            </div>` : ``}
                                    </div>
                                </div>
                            </div>
                        </div>`;
                });

                $('#notificationList').html(html);
            }

            function updateFooter(json) {
                const info = table.page.info();
                $('#dtInfo').text(
                    `Showing ${info.end - info.start} of ${info.recordsFiltered} notifications`
                );
            }

            function dotColor(type) {
                return {
                    important: 'bg-red-500',
                    scheme_update: 'bg-green-500',
                    application_status: 'bg-blue-500',
                    event: 'bg-purple-500',
                    deadline: 'bg-yellow-500'
                }[type] || 'bg-gray-400';
            }

            function badge(type) {
                return type === 'important'
                    ? `<span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded mr-2">Important</span>`
                    : '';
            }

            function escape(text) {
                return $('<div>').text(text).html();
            }


        });
    </script>



@endpush