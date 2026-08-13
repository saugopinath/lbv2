<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPageVisitLog;
use App\Models\UserRoleSchemeOfficeMapping;
use Illuminate\Support\Facades\Log;

use App\Attributes\Loggable;

class PageVisitlog
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        if ($request->is('livewire/update')) {

            // ⭐ Link Livewire Action to the current Page Visit
            $referrer = $request->headers->get('referer');
            $latestVisit = UserPageVisitLog::where('session_id', $request->session()->getId())
                ->where('url', $referrer)
                ->latest()
                ->first();

            if ($latestVisit) {
                app()->instance('user_page_visit_log_id', (string) $latestVisit->id);
            }

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
                    $componentClass = 'App\\Livewire\\' . implode('\\', array_map(fn($part) => \Illuminate\Support\Str::studly($part), explode('.', $componentName)));
                    $state = $snapshot['data'] ?? [];

                    $cleanState = [];

                    foreach ($state as $key => $value) {

                        // Livewire model serialization
                        if (is_array($value) && isset($value[1]['key'])) {
                            $cleanState[$key] = $value[1]['key'];
                            continue;
                        }

                        if (is_array($value)) {
                            $cleanState[$key] = json_encode($value);
                            continue;
                        }

                        $cleanState[$key] = $value;
                    }

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

                        // merge with params
                        $params = array_merge(
                            $call['params'] ?? [],
                            $cleanState
                        );

                        $metadata = $this->getLoggingMetadata($componentClass, $methodName);
                        $createdActionLogs[] = \App\Models\LivewireActionLog::create([
                            'user_id' => Auth::id(),
                            'session_id' => $request->session()->getId(),
                            'url' => $request->headers->get('referer'),
                            'ip' => $request->ip(),
                            'component_name' => $componentName,
                            'method_name' => $methodName,
                            'log_level' => $metadata['level'],
                            'log_nickname' => $metadata['nickname'],
                            'user_page_visit_log_id' => app()->has('user_page_visit_log_id') ? app('user_page_visit_log_id') : null,
                            'request_payload' => [
                                'params' => $params,
                                'updates' => $component['updates'] ?? [],
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


        if (!Auth::check()) {
            return $next($request);
        }
        if (
            $request->is('livewire/*') ||
            $request->is('api/*') ||
            $request->is('_debugbar/*') ||
            $request->is('css/*') ||
            $request->is('js/*') ||
            $request->is('images/*') ||
            $request->is('favicon.ico') ||
            $request->is('otp-validate*') ||
            $request->is('login*') ||
            $request->is('logout*')


        ) {
            return $next($request);
        }

        // ⭐ Pre-create Page Visit Log to get ID for Audits
        $pageVisitLog = null;
        try {
            $agent = new Agent();
            $browser = $agent->browser();
            $userId = Auth::id();
            $userRole = UserRoleSchemeOfficeMapping::where('user_id', $userId)
                ->first()
                ->role_id ?? null;

            $route = $request->route();
            $controller = $route ? $route->getControllerClass() : null;
            $action = $route ? $route->getActionMethod() : null;
            $metadata = $this->getLoggingMetadata($controller, $action);

            $referrer = $request->headers->get('referer');
            if ($referrer && str_contains($referrer, 'otp-validate')) {
                $referrer = null;
            }

            $pageVisitLog = UserPageVisitLog::create([
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
                'referrer' => $referrer,
                'session_id' => $request->session()->getId(),
                'log_level' => $metadata['level'],
                'log_nickname' => $metadata['nickname'],
            ]);

            app()->instance('user_page_visit_log_id', (string) $pageVisitLog->id);
        } catch (\Exception $e) {
            Log::error('Pre-request page visit log failed: ' . $e->getMessage());
        }

        $response = $next($request);

        if ($pageVisitLog) {
            try {
                $requestPayload = [
                    'body' => $request->except(['password', '_token']),
                    'route_params' => optional($request->route())->parameters(),
                ];
                $responsePayload = [
                    'status' => $response->getStatusCode(),
                    'headers' => $response->headers->all(),
                ];

                $pageVisitLog->update([
                    'request_payload' => $requestPayload,
                    'response_payload' => $responsePayload,
                    'status_code' => $response->getStatusCode(),
                ]);
            } catch (\Exception $e) {
                Log::error('Post-request page visit log update failed: ' . $e->getMessage());
            }
        }

        return $response;
    }

    /**
     * Resolve logging metadata from PHP Attributes.
     */
    private function getLoggingMetadata($class, $method = null)
    {
        $metadata = [
            'level' => 'N',
            'nickname' => null,
        ];
        if (!$class) return $metadata;
        try {
            $reflectionClass = new \ReflectionClass($class);

            // Check Class Level Attributes
            $classAttributes = $reflectionClass->getAttributes(Loggable::class);
            if (!empty($classAttributes)) {
                $instance = $classAttributes[0]->newInstance();
                $metadata['level'] = $instance->level;
                $metadata['nickname'] = $instance->nickname;
            }

            // Check Method Level Attributes (overwrites class level if present)
            if ($method && $reflectionClass->hasMethod($method)) {
                $reflectionMethod = $reflectionClass->getMethod($method);
                $methodAttributes = $reflectionMethod->getAttributes(Loggable::class);
                if (!empty($methodAttributes)) {
                    $instance = $methodAttributes[0]->newInstance();
                    $metadata['level'] = $instance->level;
                    if ($instance->nickname) {
                        $metadata['nickname'] = $instance->nickname;
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail if reflection is not possible
        }

        return $metadata;
    }
}
