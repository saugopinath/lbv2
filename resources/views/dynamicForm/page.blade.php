<x-layouts.app>
    <div class="container mx-auto py-6">
        <h1 class="text-2xl font-bold mb-6 text-indigo-700">
            Dynamic Form
        </h1>
        <livewire:dynamic-form.render-dynamic-form 
            :scheme-id="$scheme_id"
            :applicationId="$applicationId"
        />
    </div>
</x-layouts.app>
