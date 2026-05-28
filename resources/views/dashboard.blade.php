@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1>Dashboard SOC</h1>
    <p>Resumen operativo de incidentes de seguridad</p>
</div>

<div class="grid-stats">
    <div class="card stat-card">
        <h3>{{ $stats['total'] }}</h3>
        <p>Total incidentes</p>
    </div>
    <div class="card stat-card">
        <h3>{{ $stats['open'] }}</h3>
        <p>Abiertos</p>
    </div>
    <div class="card stat-card">
        <h3>{{ $stats['investigating'] }}</h3>
        <p>En investigación</p>
    </div>
    <div class="card stat-card">
        <h3 style="color: #fca5a5;">{{ $stats['critical'] }}</h3>
        <p>Críticos</p>
    </div>
    @if($usersCount !== null)
    <div class="card stat-card">
        <h3>{{ $usersCount }}</h3>
        <p>Usuarios registrados</p>
    </div>
    @endif
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.75rem;">
    @foreach(['critical', 'high', 'medium', 'low'] as $level)
        <div class="card stat-card">
            <h3>{{ $severityBreakdown[$level] ?? 0 }}</h3>
            <p>Incidentes {{ ucfirst($level) }}</p>
        </div>
    @endforeach
</div>

<div style="display: grid; grid-template-columns: 1fr minmax(280px, 420px) 1fr; gap: 0.75rem; margin-bottom: 1rem; align-items: start;">
    <div class="card">
        <h2 style="font-size: 1rem; margin-bottom: 0.75rem;">Incidentes por estado</h2>
        @php $maxStatus = max(1, (int) collect($statusBreakdown)->max()); @endphp
        @foreach(['open', 'investigating', 'resolved', 'closed'] as $status)
            @php
                $value = (int) ($statusBreakdown[$status] ?? 0);
                $width = ($value / $maxStatus) * 100;
            @endphp
            <div style="margin-bottom: 0.75rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.25rem;">
                    <span style="text-transform: uppercase; color: #8b9bb0;">{{ $status }}</span>
                    <strong>{{ $value }}</strong>
                </div>
                <div style="height: 8px; background: #1a2330; border-radius: 999px;">
                    <div style="height: 8px; width: {{ $width }}%; background: #3b82f6; border-radius: 999px;"></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card" style="padding: 0.9rem;">
        <div style="display: flex; justify-content: space-between; gap: 0.75rem; align-items: baseline; margin-bottom: 0.5rem;">
            <h2 style="font-size: 0.95rem;">Heatmap semanal</h2>
            <p style="color: #8b9bb0; font-size: 0.75rem;">por criticidad</p>
        </div>

        <div style="overflow: hidden;">
            <div style="display: grid; grid-template-columns: repeat({{ $weeklyHeatmap->count() }}, 10px); column-gap: 2px; align-items: start; justify-content: start;">
                @foreach($weeklyHeatmap as $week)
                    <div style="display: grid; row-gap: 2px;">
                        @foreach($week['days'] as $day)
                            @php
                                $bg = match($day['max_score']) {
                                    1 => '#22c55e', // low
                                    2 => '#facc15', // medium
                                    3 => '#f97316', // high
                                    4 => '#ef4444', // critical or many high/critical
                                    default => '#1a2330',
                                };
                            @endphp
                            <div
                                title="{{ $day['label'] }} | Incidentes: {{ $day['count'] }}"
                                style="height: 10px; width: 10px; border-radius: 2px; background: {{ $bg }};"
                            ></div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; align-items: center; gap: 0.25rem; margin-top: 0.5rem; color: #8b9bb0; font-size: 0.7rem;">
            <span>Baja</span>
            <span style="height: 8px; width: 8px; border-radius: 2px; background: #22c55e; display: inline-block;"></span>
            <span style="height: 8px; width: 8px; border-radius: 2px; background: #facc15; display: inline-block;"></span>
            <span style="height: 8px; width: 8px; border-radius: 2px; background: #f97316; display: inline-block;"></span>
            <span style="height: 8px; width: 8px; border-radius: 2px; background: #ef4444; display: inline-block;"></span>
            <span>Alta</span>
        </div>
    </div>

    <div class="card">
        <h2 style="font-size: 1rem; margin-bottom: 0.75rem;">Actividad por usuario</h2>
        @php $maxUser = max(1, (int) collect($activityByUser)->pluck('total')->max()); @endphp
        @forelse($activityByUser as $row)
            @php $width = ($row['total'] / $maxUser) * 100; @endphp
            <div style="margin-bottom: 0.75rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 0.25rem;">
                    <span>{{ $row['name'] }}</span>
                    <strong>{{ $row['total'] }}</strong>
                </div>
                <div style="height: 8px; background: #1a2330; border-radius: 999px;">
                    <div style="height: 8px; width: {{ $width }}%; background: #22c55e; border-radius: 999px;"></div>
                </div>
            </div>
        @empty
            <p style="color: #8b9bb0;">Sin datos de actividad.</p>
        @endforelse
    </div>
</div>

<div class="toolbar">
    <h2 style="font-size: 1rem; font-weight: 600;">Incidentes recientes</h2>
  @if(auth()->user()->isAdmin() || auth()->user()->isAnalyst())
    <a href="{{ route('incidents.create') }}" class="btn btn-primary">+ Nuevo incidente</a>
  @endif
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Severidad</th>
                <th>Estado</th>
                <th>Asignado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentIncidents as $incident)
            <tr>
                <td style="font-family: 'JetBrains Mono', monospace; color: #8b9bb0;">#{{ $incident->id }}</td>
                <td><a href="{{ route('incidents.show', $incident) }}">{{ Str::limit($incident->title, 50) }}</a></td>
                <td><span class="badge badge-{{ $incident->severity }}">{{ $incident->severity }}</span></td>
                <td><span class="badge badge-{{ $incident->status }}">{{ $incident->status }}</span></td>
                <td>{{ $incident->assignee?->name ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align: center; color: #8b9bb0; padding: 2rem;">No hay incidentes</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="card" style="margin-top: 1rem;">
    <h2 style="font-size: 1rem; margin-bottom: 0.75rem;">Actividad reciente</h2>
    @forelse($recentActivity as $activity)
        <div style="padding: 0.65rem 0; border-bottom: 1px solid #2a3544;">
            <div style="display: flex; justify-content: space-between; gap: 0.5rem;">
                <p style="font-size: 0.9rem;">{{ $activity->message }}</p>
                <span style="color: #8b9bb0; font-size: 0.75rem; white-space: nowrap;">
                    {{ $activity->created_at->format('d/m H:i') }}
                </span>
            </div>
        </div>
    @empty
        <p style="color: #8b9bb0;">Sin actividad registrada.</p>
    @endforelse
</div>

<div style="margin-top: 1rem;">
    <a href="{{ route('incidents.index') }}" class="btn btn-ghost">Ver todos los incidentes →</a>
</div>
@endsection
