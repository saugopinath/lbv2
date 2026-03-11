<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPageVisitLog;
use App\Models\UserRoleSchemeOfficeMapping;
use Illuminate\Support\Facades\Log;

class PageVisitlog
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        if ($request->is('livewire/update')) {

            $payload = json_decode($request->getContent(), true) ?? [];
            $components = $payload['components'] ?? [];

            $createdActionLogs = [];

            try {
                foreach ($components as $component) {

                    $calls = $component['calls'] ?? [];
                    if (empty($calls)) {
                        continue;
                    }

                    $snapshot = is_string($component['snapshot'] ?? null)
                        ? json_decode($component['snapshot'], true)
                        : ($component['snapshot'] ?? []);

                    $componentName = $snapshot['memo']['name'] ?? 'unknown';

                    foreach ($calls as $call) {

                        $methodName = $call['method'] ?? 'unknown';

                        if (in_array($methodName, [
                            '__dispatch',
                            '__call',
                            '__set',
                            '_startUpload',
                            '_finishUpload',
                            '_cancelUpload'
                            
                        ])) {
                            continue;
                        }

                        $createdActionLogs[] = \App\Models\LivewireActionLog::create([
                            'user_id' => Auth::id(),
                            'session_id' => $request->session()->getId(),
                            'url' => $request->headers->get('referer'),
                            'ip' => $request->ip(),
                            'component_name' => $componentName,
                            'method_name' => $methodName,
                            'request_payload' => [
                                'params' => $call['params'] ?? [],
                                'updates' => $component['updates'] ?? []
                            ],
                            'response_payload' => null,
                        ]);
                    }
                }

                if (!empty($createdActionLogs)) {
                    app()->instance('livewire_action_log_id', (string) $createdActionLogs[0]->id);
                }
            } catch (\Exception $e) {
                Log::error('Livewire action log (pre-request) failed: ' . $e->getMessage());
            }

            $response = $next($request);

            try {

                $responseContent = $response->getContent();
                $responseData = json_decode($responseContent, true) ?? [];
                $responseComponents = $responseData['components'] ?? [];

                foreach ($createdActionLogs as $log) {

                    $effects = [];

                    foreach ($responseComponents as $rc) {

                        $rcSnapshot = is_string($rc['snapshot'] ?? null)
                            ? json_decode($rc['snapshot'], true)
                            : ($rc['snapshot'] ?? []);

                        if (
                            isset($rcSnapshot['memo']['name']) &&
                            $rcSnapshot['memo']['name'] === $log->component_name
                        ) {
                            $effects = $rc['effects'] ?? [];
                            break;
                        }
                    }

                    unset($effects['html']);

                    if (isset($effects['redirect'])) {
                        $effects['_action'] = 'redirect';
                        $effects['_redirect_to'] = $effects['redirect'];
                        unset($effects['redirect']);
                    }

                    if (empty($effects) && isset($responseData['dispatches'])) {
                        $effects['dispatches'] = $responseData['dispatches'];
                    }

                    $log->update([
                        'response_payload' => $effects
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Livewire action log (post-request) failed: ' . $e->getMessage());
            }

            return $response;
        }

        if (
            $request->is('livewire/*') ||
            $request->is('api/*') ||
            $request->is('_debugbar/*') ||
            $request->is('css/*') ||
            $request->is('js/*') ||
            $request->is('images/*') ||
            $request->is('favicon.ico')
        ) {
            return $next($request);
        }

        $response = $next($request);

        try {

            $agent = new Agent();
            $browser = $agent->browser();

            $userId = Auth::id();

            $userRole = UserRoleSchemeOfficeMapping::where('user_id', $userId)
                ->first()
                ->role_id ?? null;

            UserPageVisitLog::create([
                'visit_time' => now(),
                'user_id' => $userId,
                'user_role_id' => $userRole,
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'platform' => $agent->platform(),
                'browser' => $browser,
                'browser_version' => $agent->version($browser),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'referrer' => $request->headers->get('referer'),
                'session_id' => $request->session()->getId(),
            ]);
        } catch (\Exception $e) {

            Log::error('Page visit log failed: ' . $e->getMessage());
        }

        return $response;
    }
}
