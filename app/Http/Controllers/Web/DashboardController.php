<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Incident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $incidentQuery = Incident::query();

        if (! $user->isAdmin()) {
            $incidentQuery->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id);
            });
        }

        $stats = [
            'total' => (clone $incidentQuery)->count(),
            'open' => (clone $incidentQuery)->where('status', 'open')->count(),
            'investigating' => (clone $incidentQuery)->where('status', 'investigating')->count(),
            'critical' => (clone $incidentQuery)->where('severity', 'critical')->count(),
        ];

        $recentIncidents = (clone $incidentQuery)
            ->with(['creator', 'assignee'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $severityBreakdown = (clone $incidentQuery)
            ->selectRaw('severity, COUNT(*) as total')
            ->groupBy('severity')
            ->pluck('total', 'severity');

        $statusBreakdown = (clone $incidentQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $weeklyHeatmap = $this->buildWeeklyHeatmap(clone $incidentQuery);
        $activityByUser = collect();

        $recentActivity = collect();
        if (Schema::hasTable('activity_logs')) {
            $recentActivity = ActivityLog::query()
                ->with(['actor', 'incident'])
                ->when(! $user->isAdmin(), function ($query) use ($user) {
                    $query->whereHas('incident', function ($q) use ($user) {
                        $q->where('assigned_to', $user->id)
                            ->orWhere('created_by', $user->id);
                    });
                })
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            $activityByUser = ActivityLog::query()
                ->selectRaw('actor_id, COUNT(*) as total')
                ->whereNotNull('actor_id')
                ->when(! $user->isAdmin(), function ($query) use ($user) {
                    $query->whereHas('incident', function ($q) use ($user) {
                        $q->where('assigned_to', $user->id)
                            ->orWhere('created_by', $user->id);
                    });
                })
                ->groupBy('actor_id')
                ->orderByDesc('total')
                ->with('actor:id,name')
                ->limit(6)
                ->get()
                ->map(fn ($row) => [
                    'name' => $row->actor?->name ?? 'Sistema',
                    'total' => (int) $row->total,
                ]);
        }

        return view('dashboard', [
            'stats' => $stats,
            'recentIncidents' => $recentIncidents,
            'severityBreakdown' => $severityBreakdown,
            'statusBreakdown' => $statusBreakdown,
            'weeklyHeatmap' => $weeklyHeatmap,
            'activityByUser' => $activityByUser,
            'recentActivity' => $recentActivity,
            'usersCount' => $user->isAdmin() ? User::count() : null,
        ]);
    }

    private function buildWeeklyHeatmap($incidentQuery): Collection
    {
        $startDate = now()->startOfWeek(Carbon::MONDAY)->subWeeks(11);
        $endDate = now()->endOfWeek(Carbon::SUNDAY);

        $incidents = (clone $incidentQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['severity', 'created_at']);

        $severityScore = [
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            'critical' => 4,
        ];

        $days = collect();
        $cursor = $startDate->copy()->startOfDay();
        while ($cursor->lte($endDate)) {
            $dateKey = $cursor->format('Y-m-d');
            $forDay = $incidents->filter(fn ($incident) => $incident->created_at->format('Y-m-d') === $dateKey);
            $maxScore = $forDay
                ->map(fn ($incident) => $severityScore[$incident->severity] ?? 1)
                ->max() ?? 0;

            $highCriticalCount = $forDay->filter(fn ($incident) => in_array($incident->severity, ['high', 'critical'], true))->count();
            if ($highCriticalCount >= 2) {
                $maxScore = 4;
            }

            $days->push([
                'date' => $dateKey,
                'label' => $cursor->format('d M'),
                'count' => $forDay->count(),
                'max_score' => $maxScore,
                'day_name' => $cursor->isoFormat('ddd'),
                'month_name' => $cursor->isoFormat('MMM'),
            ]);

            $cursor->addDay();
        }

        return $days
            ->chunk(7)
            ->map(fn ($week) => [
                'month' => $week->first()['month_name'],
                'days' => $week->values(),
            ])
            ->values();
    }
}
