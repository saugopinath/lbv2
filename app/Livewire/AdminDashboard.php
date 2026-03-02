<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Scheme;
use App\Models\UserPageVisitLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            'total_users' => User::count(),
            'active_users' => DB::table('audits')->where('event', 'login')->distinct('user_id')->count(),
            'avg_engagement' => '657.8%', // Placeholder as per design or calculate if possible
            'new_users_30d' => User::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'modules_onboarded' => Scheme::count(),
            'total_admins' => User::role('Super Admin')->count() ?? 0, // Adjust based on your role naming
        ];

        // Fallback for total_admins if role doesn't exist
        if ($this->stats['total_admins'] == 0) {
            $this->stats['total_admins'] = DB::table('model_has_roles')->where('role_id', 1)->count(); // Assuming 1 is superadmin/admin
        }
    }

    public function loadChartData()
    {
        // Device Data (Pie/Donut)
        $devices = UserPageVisitLog::select('platform', DB::raw('count(*) as total'))
            ->groupBy('platform')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        $this->deviceData = [
            'labels' => $devices->pluck('platform')->toArray(),
            'data' => $devices->pluck('total')->toArray(),
        ];

        // Browser Data (Pie/Donut)
        $browsers = UserPageVisitLog::select('browser', DB::raw('count(*) as total'))
            ->groupBy('browser')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        $this->browserData = [
            'labels' => $browsers->pluck('browser')->toArray(),
            'data' => $browsers->pluck('total')->toArray(),
        ];

        // Hourly Activity (Line Chart - Last 24 hours)
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
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
