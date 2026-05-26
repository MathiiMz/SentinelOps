<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\Request;

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

        return view('dashboard', [
            'stats' => $stats,
            'recentIncidents' => $recentIncidents,
            'usersCount' => $user->isAdmin() ? User::count() : null,
        ]);
    }
}
