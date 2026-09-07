{{-- 
    Component: Form Error Summary Banner
    Purpose: Accepts server-side validation error bags or arrays, normalizes them into key-value pairs,
             and dynamically renders them alongside client-side errors via Alpine.js.
--}}
@props(['errors' => []])

@php
    /**
     * Normalizes error inputs into a flat associative array: ['field_name' => 'Error message'].
     * Standardizes input from either Laravel's ViewErrorBag or raw PHP arrays.
     */
    $normalizedErrors = [];

    if ($errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any()) {
        foreach ($errors->keys() as $key) {
            $normalizedErrors[$key] = $errors->first($key);
        }
    } elseif (is_array($errors)) {
        $normalizedErrors = $errors;
    }

    // Illuminate\Support\Facades\Log::info($normalizedErrors);

@endphp

{{-- 
    Main Alert Container
    - x-cloak: Hides element until Alpine initializes to prevent UI flashing.
    - x-data: Instantiates 'formErrorBanner' Alpine component, passing normalized server errors safely via JSON.
    - x-show: Dynamically shows/hides the banner depending on whether active errors exist.
--}}
<div class="p-4 mb-4 rounded-lg bg-red-50 border-l-4 border-red-600 shadow-sm" id="form-error-summary" role="alert" wire:key="error-banner-{{ md5(json_encode($normalizedErrors)) }}" x-cloak x-data="formErrorBanner({{ Js::from($normalizedErrors) }})" x-show="totalCount > 0">

    {{-- Error Banner Header & Icon --}}
    <div class="flex items-center gap-2 mb-1">
        <svg class="h-5 w-5 text-red-600 shrink-0" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <h3 class="font-bold text-red-700 text-base">Action Required</h3>
    </div>

    {{-- Dynamic Total Count Message --}}
    <div class="pl-7 mb-2">
        <p class="text-sm text-gray-600">
            Please resolve the <span class="font-bold" x-text="totalCount + ' ' + (totalCount === 1 ? 'issue' : 'issues')"></span> listed below to proceed.
        </p>
    </div>

    <hr class="border-red-200 my-2">

    {{-- List of Active Errors --}}
    <ul class="space-y-1.5 pl-0 mt-2">
        {{--
            Alpine Loop: Iterates over active error object key/value pairs.
            - @click.prevent: Intercepts click navigation to execute smooth scrolling & DOM focus via jumpTo().
            - resolveFieldId(): Converts nested dot-notation field keys to DOM element IDs.
        --}}
        <template :key="fieldKey" x-for="(message, fieldKey) in activeErrors">
            <li class="flex items-center gap-2">
                <svg class="h-4 w-4 text-red-500 shrink-0" fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M13 5l7 7-7 7M5 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                <a :href="'#' + resolveFieldId(fieldKey)" @click.prevent="jumpTo(fieldKey)" class="text-sm font-medium text-gray-800 hover:text-red-600 hover:underline transition-colors">
                    <span x-text="message"></span>
                </a>
            </li>
        </template>
    </ul>
</div>
