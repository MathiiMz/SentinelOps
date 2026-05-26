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

<div style="margin-top: 1rem;">
    <a href="{{ route('incidents.index') }}" class="btn btn-ghost">Ver todos los incidentes →</a>
</div>
@endsection
