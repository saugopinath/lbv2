<div class="border-b border-gray-200 hover:bg-green-50 transition-colors">
    <div class="px-6 py-4">
        <div class="flex items-start">
            <div class="flex-shrink-0 mt-1">
                @php
                    $dotColor = 'bg-gray-400';
                    switch($row->type) {
                        case 'important': $dotColor = 'bg-red-500'; break;
                        case 'scheme_update': $dotColor = 'bg-green-500'; break;
                        case 'application_status': $dotColor = 'bg-blue-500'; break;
                        case 'event': $dotColor = 'bg-purple-500'; break;
                        case 'deadline': $dotColor = 'bg-yellow-500'; break;
                    }
                @endphp
                <div class="w-3 h-3 rounded-full {{ $dotColor }}"></div>
            </div>
            <div class="ml-4 flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">
                        @if($row->type === 'important')
                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded mr-2">Important</span>
                        @endif
                        {{ $row->title }}
                    </h3>
                    <span class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($row->notified_at)->diffForHumans() }}
                    </span>
                </div>
                <p class="mt-1 text-gray-600">{{ $row->message }}</p>
                @if($row->scheme_name)
                    <div class="mt-2 text-sm text-green-600 font-medium">
                        {{ $row->scheme_name }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
