<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\Request;

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

        $incidents = $query->orderByDesc('created_at')->paginate(12)->withQueryString();

        return view('incidents.index', compact('incidents'));
    }

    public function show(Request $request, Incident $incident)
    {
        $this->authorizeView($request, $incident);

        $incident->load(['creator', 'assignee', 'comments.user']);

        $analysts = $request->user()->isAdmin() || $request->user()->isAnalyst()
            ? User::whereIn('role', ['admin', 'analyst'])->where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('incidents.show', compact('incident', 'analysts'));
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'severity' => ['required', 'in:critical,high,medium,low'],
            'source_ip' => ['required', 'ip'],
            'affected_host' => ['required', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        Incident::create([
            ...$data,
            'status' => 'open',
            'created_by' => $request->user()->id,
        ]);

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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'severity' => ['required', 'in:critical,high,medium,low'],
            'status' => ['required', 'in:open,investigating,resolved,closed'],
            'source_ip' => ['required', 'ip'],
            'affected_host' => ['required', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $incident->update($data);

        return redirect()->route('incidents.show', $incident)
            ->with('success', 'Incidente actualizado correctamente.');
    }

    public function destroy(Request $request, Incident $incident)
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

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
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $incident->comments()->create([
            'content' => $data['content'],
            'user_id' => $request->user()->id,
        ]);

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

        $incident->update(['status' => $data['status']]);

        return back()->with('success', 'Estado actualizado.');
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
}
