<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Incident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->scopedQuery($request);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('affected_host', 'like', "%{$search}%");
            });
        }

        if ($request->filled('host')) {
            $query->where('affected_host', 'like', '%'.$request->host.'%');
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->integer('assigned_to'));
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->integer('created_by'));
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->date('created_from'));
        }

        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->date('created_to'));
        }

        $incidents = $query->orderByDesc('created_at')->paginate(12)->withQueryString();
        $users = User::query()->orderBy('name')->get(['id', 'name']);

        return view('incidents.index', compact('incidents', 'users'));
    }

    public function show(Request $request, Incident $incident)
    {
        $this->authorizeView($request, $incident);

        $incident->load(['creator', 'assignee', 'comments.user']);
        if (Schema::hasTable('activity_logs')) {
            $incident->load(['activities.actor']);
        }

        $timeline = collect();
        if (Schema::hasTable('activity_logs')) {
            $timeline = $timeline->merge(
                $incident->activities->map(function ($activity) {
                    return [
                        'type' => 'activity',
                        'created_at' => $activity->created_at,
                        'label' => $activity->message,
                        'actor' => $activity->actor?->name ?? 'Sistema',
                    ];
                })
            );
        }

        $timeline = $timeline
            ->merge(
                $incident->comments->map(function ($comment) {
                    return [
                        'type' => 'comment',
                        'created_at' => $comment->created_at,
                        'label' => $comment->content,
                        'actor' => $comment->user->name,
                    ];
                })
            )
            ->sortByDesc('created_at')
            ->values();

        $analysts = $request->user()->isAdmin() || $request->user()->isAnalyst()
            ? User::whereIn('role', ['admin', 'analyst'])->where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('incidents.show', compact('incident', 'analysts', 'timeline'));
    }

    public function create(Request $request)
    {
        $this->authorizeWrite($request);

        $analysts = User::whereIn('role', ['admin', 'analyst'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('incidents.create', compact('analysts'));
    }

    public function store(Request $request)
    {
        $this->authorizeWrite($request);

        $data = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:10000'],
            'severity' => ['required', 'in:critical,high,medium,low'],
            'source_ip' => ['required', 'ip'],
            'affected_host' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\.\-_]+$/'],
            'assigned_to' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_active', true)->whereIn('role', ['admin', 'analyst'])),
            ],
        ]);

        $incident = Incident::create([
            ...$data,
            'title' => $this->normalizeText($data['title']),
            'description' => $this->normalizeText($data['description']),
            'affected_host' => $this->normalizeText($data['affected_host']),
            'status' => 'open',
            'created_by' => $request->user()->id,
        ]);

        $this->logActivity(
            $request->user()->id,
            $incident->id,
            'incident.created',
            "{$request->user()->name} created incident #{$incident->id} ({$this->formatLabel($incident->severity)} severity)",
            [
                'severity' => $incident->severity,
                'status' => $incident->status,
            ]
        );

        return redirect()->route('incidents.index')
            ->with('success', 'Incidente creado correctamente.');
    }

    public function edit(Request $request, Incident $incident)
    {
        if (! $request->user()->isAdmin() && $request->user()->id !== $incident->created_by) {
            abort(403);
        }

        $analysts = User::whereIn('role', ['admin', 'analyst'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('incidents.edit', compact('incident', 'analysts'));
    }

    public function update(Request $request, Incident $incident)
    {
        if (! $request->user()->isAdmin() && $request->user()->id !== $incident->created_by) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:10000'],
            'severity' => ['required', 'in:critical,high,medium,low'],
            'status' => ['required', 'in:open,investigating,resolved,closed'],
            'source_ip' => ['required', 'ip'],
            'affected_host' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\.\-_]+$/'],
            'assigned_to' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('is_active', true)->whereIn('role', ['admin', 'analyst'])),
            ],
        ]);

        if (! $this->canTransitionStatus($incident->status, $data['status'])) {
            return back()->withInput()->with('error', 'Transición de estado no permitida para este incidente.');
        }

        $oldSeverity = $incident->severity;
        $oldStatus = $incident->status;
        $oldAssignee = $incident->assigned_to;

        $incident->update([
            ...$data,
            'title' => $this->normalizeText($data['title']),
            'description' => $this->normalizeText($data['description']),
            'affected_host' => $this->normalizeText($data['affected_host']),
        ]);

        if ($oldSeverity !== $incident->severity) {
            $this->logActivity(
                $request->user()->id,
                $incident->id,
                'incident.severity_changed',
                "{$request->user()->name} changed severity on incident #{$incident->id} from {$this->formatLabel($oldSeverity)} to {$this->formatLabel($incident->severity)}",
                [
                    'from' => $oldSeverity,
                    'to' => $incident->severity,
                ]
            );
        }

        if ($oldStatus !== $incident->status) {
            $this->logActivity(
                $request->user()->id,
                $incident->id,
                'incident.status_changed',
                "{$request->user()->name} changed status on incident #{$incident->id} from {$this->formatLabel($oldStatus)} to {$this->formatLabel($incident->status)}",
                [
                    'from' => $oldStatus,
                    'to' => $incident->status,
                ]
            );
        }

        if ($oldAssignee !== $incident->assigned_to) {
            $oldAssigneeName = $oldAssignee ? User::find($oldAssignee)?->name : 'Unassigned';
            $newAssigneeName = $incident->assignee?->name ?? 'Unassigned';
            $this->logActivity(
                $request->user()->id,
                $incident->id,
                'incident.assigned_changed',
                "{$request->user()->name} assigned incident #{$incident->id} from {$oldAssigneeName} to {$newAssigneeName}",
                [
                    'from' => $oldAssignee,
                    'to' => $incident->assigned_to,
                ]
            );
        }

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incidente actualizado correctamente.');
    }

    public function destroy(Request $request, Incident $incident)
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $this->logActivity(
            $request->user()->id,
            $incident->id,
            'incident.deleted',
            "{$request->user()->name} deleted incident #{$incident->id}"
        );

        $incident->delete();

        return redirect()->route('incidents.index')
            ->with('success', 'Incidente eliminado correctamente.');
    }

    public function storeComment(Request $request, Incident $incident)
    {
        $this->authorizeView($request, $incident);

        if ($request->user()->isViewer()) {
            abort(403);
        }

        $data = $request->validate([
            'content' => ['required', 'string', 'min:2', 'max:5000'],
        ]);

        $incident->comments()->create([
            'content' => $this->normalizeText($data['content']),
            'user_id' => $request->user()->id,
        ]);

        $this->logActivity(
            $request->user()->id,
            $incident->id,
            'incident.comment_added',
            "{$request->user()->name} added a timeline comment on incident #{$incident->id}"
        );

        return back()->with('success', 'Comentario agregado.');
    }

    public function updateStatus(Request $request, Incident $incident)
    {
        $this->authorizeView($request, $incident);

        if ($request->user()->isViewer()) {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required', 'in:open,investigating,resolved,closed'],
        ]);

        if (! $this->canTransitionStatus($incident->status, $data['status'])) {
            return back()->with('error', 'Transición de estado no permitida para este incidente.');
        }

        $oldStatus = $incident->status;
        $incident->update(['status' => $data['status']]);

        if ($oldStatus !== $incident->status) {
            $this->logActivity(
                $request->user()->id,
                $incident->id,
                'incident.status_changed',
                "{$request->user()->name} changed status on incident #{$incident->id} from {$this->formatLabel($oldStatus)} to {$this->formatLabel($incident->status)}",
                [
                    'from' => $oldStatus,
                    'to' => $incident->status,
                ]
            );
        }

        return back()->with('success', 'Estado actualizado.');
    }

    public function exportPdf(Request $request, Incident $incident)
    {
        $this->authorizeView($request, $incident);

        $incident->load(['creator', 'assignee', 'comments.user']);
        if (Schema::hasTable('activity_logs')) {
            $incident->load(['activities.actor']);
        }

        $timeline = collect();
        if (Schema::hasTable('activity_logs')) {
            $timeline = $timeline->merge(
                $incident->activities->map(fn ($activity) => [
                    'created_at' => $activity->created_at,
                    'line' => $activity->message,
                ])
            );
        }

        $timeline = $timeline
            ->merge(
                $incident->comments->map(fn ($comment) => [
                    'created_at' => $comment->created_at,
                    'line' => $comment->user->name.': '.$comment->content,
                ])
            )
            ->sortBy('created_at')
            ->values();

        $this->logActivity(
            $request->user()->id,
            $incident->id,
            'incident.pdf_exported',
            "{$request->user()->name} exported incident #{$incident->id} to PDF"
        );

        $pdf = new \TCPDF();
        $pdf->SetCreator('SentinelOps');
        $pdf->SetAuthor($request->user()->name);
        $pdf->SetTitle("Incidente #{$incident->id}");
        $pdf->SetMargins(14, 14, 14);
        $pdf->AddPage();

        $html = view('incidents.pdf', [
            'incident' => $incident,
            'timeline' => $timeline,
        ])->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        return response($pdf->Output("incident-{$incident->id}.pdf", 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=incident-{$incident->id}.pdf",
        ]);
    }

    private function scopedQuery(Request $request)
    {
        $query = Incident::with(['creator', 'assignee']);

        if (! $request->user()->isAdmin()) {
            $query->where(function ($q) use ($request) {
                $q->where('assigned_to', $request->user()->id)
                    ->orWhere('created_by', $request->user()->id);
            });
        }

        return $query;
    }

    private function authorizeView(Request $request, Incident $incident): void
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->id === $incident->created_by || $user->id === $incident->assigned_to) {
            return;
        }

        abort(403);
    }

    private function authorizeWrite(Request $request): void
    {
        if (! $request->user()->isAdmin() && ! $request->user()->isAnalyst()) {
            abort(403);
        }
    }

    private function canTransitionStatus(string $from, string $to): bool
    {
        $allowedTransitions = [
            'open' => ['open', 'investigating', 'resolved', 'closed'],
            'investigating' => ['investigating', 'resolved', 'closed'],
            'resolved' => ['resolved', 'closed'],
            'closed' => ['closed'],
        ];

        return in_array($to, $allowedTransitions[$from] ?? [], true);
    }

    private function normalizeText(string $value): string
    {
        return trim(strip_tags($value));
    }

    private function logActivity(?int $actorId, ?int $incidentId, string $eventType, string $message, array $meta = []): void
    {
        if (! Schema::hasTable('activity_logs')) {
            return;
        }

        ActivityLog::create([
            'actor_id' => $actorId,
            'incident_id' => $incidentId,
            'event_type' => $eventType,
            'message' => $message,
            'meta' => $meta,
        ]);
    }

    private function formatLabel(?string $value): string
    {
        return strtoupper(str_replace('_', ' ', (string) $value));
    }
}
