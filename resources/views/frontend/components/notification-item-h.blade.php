<!-- Notification Item Dynamic -->
<div class="inline-flex items-center gap-3 px-4 py-2 bg-white rounded-lg shadow-sm border-l-4 border-indigo-500 hover:border-indigo-600 transition-colors">
    @if($notification->notified_at && $notification->notified_at->greaterThanOrEqualTo(now()->subHours(12)))
        <span class="px-2 py-1 bg-red-100 text-red-600 text-[10px] font-bold rounded uppercase tracking-wider animate-pulse">NEW</span>
    @endif
    <span class="font-medium text-gray-800 text-sm">
        <span class="text-indigo-600 font-bold uppercase text-[10px] mr-1">[{{ $notification->scheme_name ?? 'GENERAL' }}]</span>
        {{ str($notification->title . ': ' . $notification->message)->limit(100) }}
    </span>
    <a href="{{ route('home.notification') }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold flex items-center gap-1 group">
        Read More <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
    </a>
</div>
