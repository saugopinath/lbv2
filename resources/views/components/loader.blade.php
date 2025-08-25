{{-- This component will display your custom spinner SVG --}}
<div {{ $attributes->merge(['class' => 'flex items-center justify-center']) }}>
    <img src="{{ asset('images/spinner.svg') }}" alt="Loading..." class="h-5 w-5" />
</div>
