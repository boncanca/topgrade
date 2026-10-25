<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Number;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class LogsController
{
    /**
     * Display the audit activity and system error logs.
     */
    public function index(Request $request): Response
    {
        $tab = $request->string('tab', 'audit')->value();
        $search = $request->string('search')->trim()->value();
        $level = $request->string('level', 'all')->value();

        // 1. Audit Activity Logs (Spatie Activitylog)
        $auditLogsQuery = Activity::with('causer')
            ->when($search && $tab === 'audit', function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('description', 'like', "%{$search}%")
                        ->orWhere('log_name', 'like', "%{$search}%")
                        ->orWhere('event', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id');

        $auditLogs = $auditLogsQuery->paginate(20)->withQueryString()->through(fn (Activity $act) => [
            'id' => $act->id,
            'log_name' => $act->log_name ?? 'default',
            'description' => $act->description,
            'event' => $act->event ?? 'action',
            'subject_type' => class_basename($act->subject_type ?? ''),
            'subject_id' => $act->subject_id,
            'causer_name' => $act->causer?->name ?? 'System / Guest',
            'causer_email' => $act->causer?->email,
            'properties' => $act->properties?->toArray() ?? [],
            'created_at' => $act->created_at?->format('d M Y, H:i:s'),
            'relative_time' => $act->created_at?->diffForHumans(),
        ]);

        // 2. System / Laravel Application Logs
        $systemLogs = $this->parseSystemLogs($search, $level);
        $logPath = storage_path('logs/laravel.log');
        $systemLogSize = File::exists($logPath) ? Number::fileSize(File::size($logPath)) : '0 B';

        return Inertia::render('Logs/Index', [
            'tab' => $tab,
            'auditLogs' => $auditLogs,
            'systemLogs' => $systemLogs,
            'systemLogSize' => $systemLogSize,
            'filters' => [
                'search' => $search,
                'level' => $level,
                'tab' => $tab,
            ],
            'stats' => [
                'totalAudit' => Activity::count(),
                'totalSystem' => count($systemLogs),
            ],
        ]);
    }

    /**
     * Parse recent entries from storage/logs/laravel.log.
     *
     * @return array<int, array{id: int, timestamp: string, level: string, message: string, stack_trace: ?string}>
     */
    protected function parseSystemLogs(?string $search = null, string $level = 'all'): array
    {
        $logPath = storage_path('logs/laravel.log');

        if (! File::exists($logPath)) {
            return [];
        }

        $content = File::get($logPath);

        if (empty(trim($content))) {
            return [];
        }

        // Match log entry: [YYYY-MM-DD HH:MM:SS] env.LEVEL: Message {stack trace}
        $pattern = '/\[(?P<date>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(?P<level>[A-Z]+): (?P<message>.*?)(?=(?:\n\[\d{4}-\d{2}-\d{2})|\z)/s';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $parsed = [];
        $index = 1;

        // Take last 100 entries in reverse chronological order
        $recentMatches = array_reverse(array_slice($matches, -100));

        foreach ($recentMatches as $entry) {
            $entryLevel = strtolower($entry['level'] ?? 'info');
            $fullMessage = trim($entry['message'] ?? '');

            if ($level !== 'all' && $entryLevel !== strtolower($level)) {
                continue;
            }

            if ($search && ! str_contains(strtolower($fullMessage), strtolower($search))) {
                continue;
            }

            $lines = explode("\n", $fullMessage);
            $primaryMessage = $lines[0] ?? '';
            $stackTrace = count($lines) > 1 ? implode("\n", array_slice($lines, 1)) : null;

            $parsed[] = [
                'id' => $index++,
                'timestamp' => $entry['date'] ?? '',
                'level' => strtoupper($entry['level'] ?? 'INFO'),
                'message' => $primaryMessage,
                'stack_trace' => $stackTrace,
            ];
        }

        return $parsed;
    }

    /**
     * Clear the system or audit log records.
     */
    public function clear(Request $request): RedirectResponse
    {
        $type = $request->input('type', 'system');

        if ($type === 'audit') {
            Activity::truncate();

            activity()
                ->causedBy($request->user())
                ->log('Cleared activity audit trail');

            return redirect()->route('dashboard.logs.index', ['tab' => 'audit'])
                ->with('success', 'Activity audit trail cleared successfully.');
        }

        $logPath = storage_path('logs/laravel.log');

        if (File::exists($logPath)) {
            File::put($logPath, '');
        }

        activity()
            ->causedBy($request->user())
            ->log('Cleared system application logs');

        return redirect()->route('dashboard.logs.index', ['tab' => 'system'])
            ->with('success', 'System log file cleared successfully.');
    }
}
