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
@endpush
@section('content')
<!-- Main Header -->
@include('frontend.components.top-header')

<!-- Main Header -->
@include('frontend.components.header')

<!-- Notification Section -->
<section id="notifications" class="maautoauto px-4 py-12">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Section Header -->
        <div class="bg-indigo-700 text-white px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-bell text-xl mr-3"></i>
                    <h2 class="text-2xl font-bold">Notifications</h2>
                </div>
                <div class="flex flex-col md:flex-row md:items-center gap-2">
                    @if($latest)
                    <div class="flex items-center space-x-2">
                        <span class="text-xs bg-red-600 px-2 py-0.5 rounded-full animate-pulse shadow-sm">
                            <i class="fas fa-bolt text-[10px] mr-1"></i> NEWS
                        </span>
                        <span class="text-sm font-medium text-indigo-100 hidden sm:inline-block">
                            {{ str($latest->title)->limit(50) }}
                        </span>
                        <span class="text-[10px] text-indigo-300 ml-2">
                            {{ $latest->notified_at ? $latest->notified_at->diffForHumans() : '' }}
                        </span>
                    </div>
                    @else
                    <span class="text-sm bg-indigo-800 px-3 py-1 rounded-full">Updates</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Livewire Rappasoft Notifications Table -->
        <div class="p-4 sm:p-6 bg-gray-50 border-t">
            <livewire:frontend.home.notification-table />
        </div>

    </div>
</section>

<!--Footer-->
@include('frontend.layouts.footer')
@endsection