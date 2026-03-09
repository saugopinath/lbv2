<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use App\Models\UserPageVisitLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DailyUserActivity extends Component
{
    use WithPagination;

    public $dateRange;
    public $name;
    public $username;

    protected $queryString = [
        'name' => ['except' => ''],
        'username' => ['except' => ''],
        'dateRange' => ['except' => ''],
    ];

    public function updating($name)
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Activity::query()
            ->from('activity_log as main_log')
            ->where('main_log.event', 'login')
            ->where('main_log.log_name', 'auth')
            ->select('main_log.*')
            ->addSelect([
                'logout_time' => Activity::select('created_at')
                    ->whereColumn('session_id', 'main_log.session_id')
                    ->where('event', 'logout')
                    ->whereColumn('created_at', '>', 'main_log.created_at')
                    ->orderBy('created_at', 'asc')
                    ->limit(1)
            ]);

        if ($this->name) {
            $query->whereRaw("properties->>'user_name' ILIKE ?", ["%{$this->name}%"]);
        }

        if ($this->username) {
            $query->whereRaw("properties->>'user_mobile' ILIKE ?", ["%{$this->username}%"]);
        }

        if ($this->dateRange) {
            try {
                $dates = explode(' to ', $this->dateRange);
                if (count($dates) === 2) {
                    $start = Carbon::parse($dates[0])->startOfDay();
                    $end = Carbon::parse($dates[1])->endOfDay();
                    $query->whereBetween('created_at', [$start, $end]);
                } else {
                    $date = Carbon::parse($this->dateRange);
                    $query->whereDate('created_at', $date);
                }
            } catch (\Exception $e) {
                // Ignore invalid date format
            }
        } else {
            // Default to today
            $query->whereDate('created_at', Carbon::today());
        }

        $activities = $query->latest()->paginate(10);

        // Fetch visited pages for each session
        $sessionIds = $activities->pluck('session_id')->filter()->unique();

        $pageLogs = UserPageVisitLog::whereIn('session_id', $sessionIds)
            ->select('session_id', 'url')
            ->get()
            ->groupBy('session_id')
            ->map(function ($logs) {
                return $logs->map(function ($log) {
                    $pageName = Str::afterLast($log->url, '/');
                    return $pageName ? Str::title(str_replace(['-', '_'], ' ', $pageName)) : 'Home';
                })->unique();
            });

        return view('livewire.daily-user-activity', [
            'activities' => $activities,
            'pageLogs' => $pageLogs
        ]);
    }
}
