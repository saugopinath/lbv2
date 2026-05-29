@php
$user = auth()->user();
$stage = request()->route('stage');

// Define Icons to avoid repetition
$icons = [
'default' =>
'
<path opacity="0.3" d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z" fill="currentColor" />',
'dashboard_smile' =>
'
<path d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z" fill="currentColor" />',
'child' =>
'
<path opacity="0.3" d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z" fill="currentColor" />
<path d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z" fill="currentColor" />',
'workflow' =>
'
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
];

// Menu Structure
$menuItems = [
[
'label' => 'Dashboard',
'route' => 'dashboard',
'icon' => $icons['default'] . $icons['dashboard_smile'],
],
[
'label' => 'ANNAPURNA Yojana',
'key' => 'LBFrom',
'icon' => $icons['default'] . $icons['dashboard_smile'],
'permission' => 'canAnyLbMenu',
'children' => [
['label' => 'Application Form', 'route' => 'form', 'permission' => 'canEntry'],
[
'label' => 'Process Application',
'route' => 'application-lists',
'permission' => 'canViewLbApplications',
],
],
],
];

// Helper to check if any child is active
$getActiveGroup = function ($item) {
if (!isset($item['children'])) {
return null;
}
foreach ($item['children'] as $child) {
$routes = [$child['route']];
if (request()->routeIs(...$routes)) {
if (isset($child['params'])) {
if (request()->fullUrl() == route($child['route'], $child['params'])) {
return $item['key'];
}
} else {
return $item['key'];
}
}
}
return null;
};

$activeMenu = null;
foreach ($menuItems as $item) {
$found = $getActiveGroup($item);
if ($found) {
$activeMenu = $found;
break;
}
}
@endphp
<aside :class="sidebar ? 'w-60' : 'w-16'"
    class="transition-all duration-300 bg-gradient-to-b from-[#b34700] via-[#e06b00] to-[#f5a623] shadow-2xl flex flex-col h-screen border-r border-orange-900/30"
    x-data="{ activeMenu: '{{ $activeMenu }}' }">
    <!-- Logo -->
    <div
        class="flex flex-col items-center border-b border-orange-900/30 bg-white py-2 {{ config('jblbConf.das_logo_class') }}">
        <img src="{{ asset('images/' . config('jblbConf.das_logo')) }}" alt="Annapurna Yojana"
            class="{{ config('jblbConf.logo_das_width') }}" />
        @if (config('jblbConf.is_ay'))
        <template x-if="sidebar">
            <div class="text-center font-bold text-sm text-orange-700">{{ config('jblbConf.headLine') }}</div>
        </template>
        @endif
    </div>
    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto mt-2 space-y-1 text-sm">

        @foreach ($menuItems as $item)
        @php
        $canShow =
        !isset($item['permission']) || \App\Helpers\WorkFlowPermissionHelper::{$item['permission']}();
        $isGroup = isset($item['children']);
        $isActive = !$isGroup && request()->routeIs($item['route']);
        $isGroupActive = $isGroup && $activeMenu === $item['key'];
        @endphp

        @if ($canShow)
        <div>
            @if ($isGroup)
            <button
                @click="activeMenu === '{{ $item['key'] }}' ? activeMenu = null : activeMenu = '{{ $item['key'] }}'"
                class="flex items-center w-full px-4 py-2.5 text-left transition-all duration-200 hover:bg-black/15 text-yellow-50 hover:text-white rounded-lg {{ $isGroupActive ? 'bg-black/20 border-l-4 border-yellow-300 text-white font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    {!! $item['icon'] !!}
                </svg>
                <span x-show="sidebar" class="mr-2 truncate">{{ $item['label'] }}</span>
                <svg class="mr-2 w-3 h-3 transition-transform duration-200"
                    :class="activeMenu === '{{ $item['key'] }}' ? 'rotate-180' : ''" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="m1 1 4 4 4-4" />
                </svg>
            </button>

            <div x-show="activeMenu === '{{ $item['key'] }}'" x-collapse x-transition class="pl-4">
                <ul>
                    @foreach ($item['children'] as $child)
                    @php
                    $canShowChild =
                    !isset($child['permission']) ||
                    \App\Helpers\WorkFlowPermissionHelper::{$child['permission']}();
                    $childUrl = isset($child['params'])
                    ? route($child['route'], $child['params'])
                    : route($child['route']);
                    $isChildActive = request()->fullUrl() == $childUrl;
                    @endphp
                    @if ($canShowChild)
                    <li>
                        <a href="{{ $childUrl }}"
                            class="flex items-center px-3 py-1.5 text-left text-yellow-100 rounded-md transition-all duration-200 hover:bg-black/15 hover:text-white {{ $isChildActive ? 'bg-black/20 text-yellow-200 font-semibold border-l-2 border-yellow-300' : '' }}">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0"
                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                {!! $icons['child'] !!}
                            </svg>
                            <span x-show="sidebar" class="truncate">{{ $child['label'] }}</span>
                        </a>
                    </li>
                    @endif
                    @endforeach
                </ul>
            </div>
            @else
            <a href="{{ route($item['route']) }}"
                class="flex items-center w-full px-4 py-2.5 text-left transition-all duration-200 hover:bg-black/15 text-yellow-50 hover:text-white rounded-lg {{ $isActive ? 'bg-black/20 border-l-4 border-yellow-300 text-white font-semibold' : '' }}">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    {!! $item['icon'] !!}
                </svg>
                <span x-show="sidebar" class="mr-2 truncate">{{ $item['label'] }}</span>
            </a>
            @endif
        </div>
        @endif
        @endforeach

    </nav>
</aside>