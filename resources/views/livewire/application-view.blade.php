<div class="bg-white dark:bg-gray-800 shadow-md rounded p-8 space-y-4">
    <div class="bg-blue-200 dark:bg-gray-800 shadow-md rounded p-2 space-y-2 text-center border border-blue-300">
        <h2 class="text-lg font-semibold">Application Name: {{ $application->full_name ?? '-' }}</h2>
        <h2 class="text-lg font-semibold">Application Id: {{ $application->application_id }}</h2>
    </div>
    <!-- PERSONAL DETAILS -->
    <x-apllicant-modal.personal-details :id="$application->id" />
    <x-apllicant-modal.contact-details :id="$application->id" />
    <x-apllicant-modal.bank-account-details :id="$application->id" />
    {{--  <x-apllicant-modal.encloser-list :id="$application->id" />  --}}
</div>
