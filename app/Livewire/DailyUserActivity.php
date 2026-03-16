<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use App\Models\UserPageVisitLog;
use App\Models\LivewireActionLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit;


class DailyUserActivity extends Component
{
    use WithPagination;

    public $dateRange;
    public $name;
    public $username;
    public $selectedSessionId;
    public $selectedUrl;
    public $selectedLogId;
    public $showAuditModal = false;

    // Livewire Action Modal
    public $showActionModal = false;
    public $actionModalSessionId = null;
    public $actionModalUrl = null;
    public $selectedActionLog = null;

    // Request/Response modal (separate from audit log)
    public $showActionRequestModal = false;
    public $selectedActionRequestLog = null;

    public $showActionAuditModal = false;
    public $actionAudits = [];
    public $showPageRequestModal = false;
    public $pageRequests = [];


    protected $queryString = [
        'name' => ['except' => ''],
        'username' => ['except' => ''],
        'dateRange' => ['except' => ''],
    ];


    public function updating($name)
    {
        $this->resetPage();
    }

    public function openAuditModal($sessionId, $url = null, $logId = null)
    {
        $this->selectedSessionId = $sessionId;
        $this->selectedUrl = $url;
        $this->selectedLogId = $logId;
        $this->showAuditModal = true;
    }

    public function closeAuditModal()
    {
        $this->showAuditModal = false;
        $this->selectedSessionId = null;
        $this->selectedUrl = null;
        $this->selectedLogId = null;
    }

    // ── Livewire Action Modal ────

    public function openActionModal($sessionId, $url)
    {
        $this->actionModalSessionId = $sessionId;
        $this->actionModalUrl = $url;
        $this->showActionModal = true;
    }

    public function closeActionModal()
    {
        $this->showActionModal = false;
        $this->actionModalSessionId = null;
        $this->actionModalUrl = null;
        $this->selectedActionLog   = null;
        $this->selectedActionRequestLog = null;
        $this->showActionRequestModal = false;
        $this->showActionAuditModal = false;
    }
    public function openPageRequestResponse($sessionId, $url)
    {
        $this->selectedSessionId = (string) $sessionId;
        $this->selectedUrl = (string) $url;
        $logs = UserPageVisitLog::where('session_id', $this->selectedSessionId)
            ->where('url', $this->selectedUrl)
            ->orderBy('visit_time')
            ->get();

        $requests = [];
        foreach ($logs as $log) {
            $requests[] = [
                'id' => $log->id,
                'session_id' => (string) $this->selectedSessionId,
                'visit_time' => $log->visit_time,
                'url' => $log->url,
                'log_level' => $log->log_level,
                'log_nickname' => $log->log_nickname,
                'request_payload' => is_string($log->request_payload)
                    ? json_decode($log->request_payload, true)
                    : $log->request_payload,

                'response_payload' => is_string($log->response_payload)
                    ? json_decode($log->response_payload, true)
                    : $log->response_payload
            ];
        }

        $this->pageRequests = $requests;
        $this->showPageRequestModal = true;
    }

    public function openActionRequestModal($actionLogId)
    {
        $actionLog = LivewireActionLog::find($actionLogId);

        if (!$actionLog) {
            $this->selectedActionRequestLog = null;
            $this->showActionRequestModal = true;
            return;
        }

        $arr = $actionLog->toArray();
        $arr['request_payload']  = is_string($arr['request_payload']  ?? null) ? json_decode($arr['request_payload'],  true) : ($arr['request_payload']  ?? []);
        $arr['response_payload'] = is_string($arr['response_payload'] ?? null) ? json_decode($arr['response_payload'], true) : ($arr['response_payload'] ?? []);

        $this->selectedActionRequestLog = $arr;
        $this->showActionRequestModal = true;
    }

    public function closeActionRequestModal()
    {
        $this->showActionRequestModal = false;
        $this->selectedActionRequestLog = null;
    }

    public function openActionAuditLog($actionLogId)
    {
        // dd($actionLogId);
        $actionLog = LivewireActionLog::find($actionLogId);
        if (!$actionLog) {
            $this->selectedActionLog = null;
            $this->actionAudits = [];
            $this->showActionAuditModal = true;
            return;
        }
        $arr = $actionLog->toArray();
        $arr['request_payload']  = is_string($arr['request_payload']  ?? null) ? json_decode($arr['request_payload'],  true) : ($arr['request_payload']  ?? []);
        $arr['response_payload'] = is_string($arr['response_payload'] ?? null) ? json_decode($arr['response_payload'], true) : ($arr['response_payload'] ?? []);
        $this->selectedActionLog = $arr;
        $nextAction = LivewireActionLog::where('session_id', $actionLog->session_id)
            ->where('created_at', '>', $actionLog->created_at)
            ->orderBy('created_at')
            ->first();
        $from = $actionLog->created_at;
        $to   = $nextAction
            ? $nextAction->created_at
            : $actionLog->created_at->addSeconds(5);
        $query = Audit::where('session_id', $actionLog->session_id);
        $query->where('livewire_action_log_id', (string) $actionLog->id);
        // $query->where('other_details', 'LIKE', '%"livewire_action_log_id":"' . $actionLog->id . '"%');
        // dd($query->toSql(), $query->getBindings());
        $this->actionAudits = $query
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get()
            ->toArray();
        $this->showActionAuditModal = true;
    }

    public function closeActionAuditLog()
    {
        $this->showActionAuditModal = false;
        $this->selectedActionLog = null;
        $this->actionAudits = [];
    }

    public function getActionLogsProperty()
    {
        // dd($this->actionModalSessionId, $this->actionModalUrl);
        // dump(
        //     $this->actionModalUrl,
        //     LivewireActionLog::where('session_id', $this->actionModalSessionId)->count()
        // );
        if (!$this->actionModalSessionId || !$this->actionModalUrl) {
            return collect();
        }
        $url = $this->actionModalUrl;
        $parsed = parse_url($url);
        $segments = explode('/', trim($parsed['path'], '/'));
        $baseUrl = $parsed['scheme'] . '://' . $parsed['host'] . '/' . ($segments[0] ?? '');
        return LivewireActionLog::where('session_id', $this->actionModalSessionId)
            ->where('url', 'LIKE', $baseUrl . '%')
            ->orderBy('created_at', 'desc')
            ->get();
        // return LivewireActionLog::where('session_id', $this->actionModalSessionId)
        //     ->where('url', 'LIKE', '%' . $this->actionModalUrl . '%')
        //     ->orderBy('created_at', 'desc')
        //     ->get();
    }
    public function getAuditsProperty()
    {
        if (!$this->selectedSessionId) {
            return collect();
        }
        $query = Audit::where('session_id', $this->selectedSessionId);
        if ($this->selectedLogId) {
            // dump($this->selectedSessionId, $this->selectedLogId, $this->selectedUrl);
            $query->where('user_page_visit_log_id', (string) $this->selectedLogId);
        } elseif ($this->selectedUrl) {
            $url = $this->selectedUrl;
            $parsed = parse_url($url);
            $path = $parsed['path'] ?? '';
            // Match the path without query strings for better results
            $matchPath = $parsed['scheme'] . '://' . $parsed['host'] . $path;

            $query->where(function ($q) use ($matchPath) {
                $q->where('other_details->url', 'LIKE', $matchPath . '%')
                    ->orWhere('other_details->referrer', 'LIKE', $matchPath . '%');
            });
        }
        return $query->latest()->get();
    }

    public function getDisplayNameFromUrl($url)
    {
        if (!$url) {
            return 'Home';
        }
        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        $segments = array_filter(explode('/', trim($path, '/')));

        $last = end($segments);
        if ($last && strlen($last) > 100 && preg_match('/^[A-Za-z0-9_\-]+$/', $last)) {
            array_pop($segments);
            $last = end($segments);
        }
        $name = $last ?: 'Home';

        return Str::title(str_replace(['-', '_'], ' ', $name));
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
                    ->limit(1),
                'last_activity' => DB::table('sessions')
                    ->select('last_activity')
                    ->whereColumn('id', 'main_log.session_id')
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
            ->select('session_id', 'url', 'log_nickname')
            ->get()
            ->groupBy('session_id')
            ->map(function ($logs) {
                return $logs->map(function ($log) {

                    $path = parse_url($log->url, PHP_URL_PATH) ?: $log->url;
                    $segments = array_filter(explode('/', trim($path, '/')));

                    $lastSegment = end($segments);

                    if ($lastSegment && strlen($lastSegment) > 100 && preg_match('/^[A-Za-z0-9_\-]+$/', $lastSegment)) {
                        array_pop($segments);
                        $lastSegment = end($segments);
                    }
                    $pageName = $lastSegment ?: 'Home';
                    return [
                        'url' => $log->url,
                        'name' => Str::title(str_replace(['-', '_'], ' ', $pageName)),
                        'log_nickname' => $log->log_nickname
                    ];
                })->unique('name')->values();
            });
        // dump($pageLogs);
        return view('livewire.daily-user-activity', [
            'activities' => $activities,
            'pageLogs' => $pageLogs
        ]);
    }
}
