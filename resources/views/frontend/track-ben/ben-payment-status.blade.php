@extends('frontend.layouts.app-template')
@section('content')
    @include('frontend.components.top-header')
    @include('frontend.components.header')

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <h2 class="text-xl font-bold text-green-700 text-center mb-6">Payment Status</h2>

        <!-- Accordion container -->
        <div class="border border-gray-200 rounded shadow-sm bg-white" x-data="{ open: true }">
            <!-- Accordion Header -->
            <button @click="open = !open" class="w-full flex items-center justify-between p-4 bg-gray-50 border-b border-gray-200 focus:outline-none">
                <div class="text-xs lg:text-sm font-bold tracking-wider text-gray-700 uppercase flex items-center gap-2">
                    <span>NAME - {{ $benPersonal->beneficiary_name }}</span> ,
                    <span>BENEFICIARY ID- {{ $benPersonal->beneficiary_id }}</span> ,
                    <span>APPLICATION ID - {{ $benPersonal->application_id }}</span>
                </div>
                <div>
                    <i class="fa-solid fa-chevron-up text-gray-500 transition-transform duration-300" :class="open ? '' : 'rotate-180'"></i>
                </div>
            </button>

            <!-- Accordion Body -->
            <div x-show="open" x-collapse class="p-6">
                <!-- Wrapper Component rendering Dropdown, Status & Datatable -->
                <livewire:frontend.track-ben.payment-status 
                    :ben_id="$beneficiary_id" 
                    :scheme_id="$scheme_id" 
                    :ben_status="$ben_status"
                    :bank_code="$encryptBankCode"
                    :ifsc="$encryptIfsc"
                />
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @endpush

    @include('frontend.layouts.footer')
@endsection