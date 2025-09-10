<div
    x-data="{ show: @entangle('show') }"
    x-show="show"
    class="fixed inset-0 bg-opacity-80 flex items-center justify-center z-50"
    style="display: none;"
>
    <img src="{{ asset('images/loader.gif') }}" alt="Loading..." class="w-16 h-16">
</div>
