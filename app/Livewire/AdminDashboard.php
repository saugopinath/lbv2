<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Scheme;
use App\Models\UserPageVisitLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class AdminDashboard extends Component
{
    public $stats = [];
    public $deviceData = [];
    public $browserData = [];
    public $hourlyVisits = [];
    public $recentActivity = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadChartData();
        $this->loadRecentActivity();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_users' => User::where('is_active', 1)->count(),
            'online_users' => User::where('is_login', 1)->count(),
            'avg_engagement' => '657.8%',
            'new_users_30d' => User::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'modules_onboarded' => Scheme::count(),
            'total_admins' => User::role('Super Admin')->count() ?? 0,
        ];

        if ($this->stats['total_admins'] == 0) {
            $this->stats['total_admins'] = DB::table('model_has_roles')->where('role_id', 1)->count();
        }
    }

    public function loadChartData()
    {
        $devices = UserPageVisitLog::select('platform', DB::raw('count(*) as total'))
            ->groupBy('platform')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        $this->deviceData = [
            'labels' => $devices->pluck('platform')->toArray(),
            'data' => $devices->pluck('total')->toArray(),
        ];

        $browsers = UserPageVisitLog::select('browser', DB::raw('count(*) as total'))
            ->groupBy('browser')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        $this->browserData = [
            'labels' => $browsers->pluck('browser')->toArray(),
            'data' => $browsers->pluck('total')->toArray(),
        ];

        $visits = UserPageVisitLog::select(DB::raw('extract(hour from visit_time) as hour'), DB::raw('count(*) as total'))
            ->where('visit_time', '>=', Carbon::now()->subDay())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $hours = [];
        $counts = [];
        for ($i = 0; $i < 24; $i++) {
            $hours[] = sprintf("%02d:00", $i);
            $found = $visits->firstWhere('hour', $i);
            $counts[] = $found ? $found->total : 0;
        }

        $this->hourlyVisits = [
            'labels' => $hours,
            'data' => $counts,
        ];
    }

    public function loadRecentActivity()
    {
        $this->recentActivity = UserPageVisitLog::with('User')
            ->orderBy('visit_time', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
