@extends('layouts.app')

@section('title', 'Incidentes')

@section('content')
<div class="page-header">
    <h1>Incidentes</h1>
    <p>Gestión y seguimiento de alertas de seguridad</p>
</div>

<div class="toolbar">
    <form class="filters" method="GET" action="{{ route('incidents.index') }}">
        <input type="search" name="q" placeholder="Buscar..." value="{{ request('q') }}">
        <input type="search" name="host" placeholder="Hostname..." value="{{ request('host') }}">
        <select name="status">
            <option value="">Todos los estados</option>
            @foreach(['open','investigating','resolved','closed'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
            @endforeach
        </select>
        <select name="severity">
            <option value="">Todas las severidades</option>
            @foreach(['critical','high','medium','low'] as $s)
                <option value="{{ $s }}" @selected(request('severity') === $s)>{{ $s }}</option>
            @endforeach
        </select>
        <select name="assigned_to">
            <option value="">Asignado a (todos)</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((string) request('assigned_to') === (string) $user->id)>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        <select name="created_by">
            <option value="">Creado por (todos)</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((string) request('created_by') === (string) $user->id)>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        <input type="date" name="created_from" value="{{ request('created_from') }}">
        <input type="date" name="created_to" value="{{ request('created_to') }}">
        <button type="submit" class="btn btn-ghost">Filtrar</button>
        @if(request()->hasAny(['q','host','status','severity','assigned_to','created_by','created_from','created_to']))
            <a href="{{ route('incidents.index') }}" class="btn btn-ghost">Limpiar</a>
        @endif
    </form>
    @if(auth()->user()->isAdmin() || auth()->user()->isAnalyst())
        <a href="{{ route('incidents.create') }}" class="btn btn-primary">+ Nuevo</a>
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
                <th>Host</th>
                <th>Asignado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incidents as $incident)
            <tr>
                <td style="font-family: 'JetBrains Mono', monospace; color: #8b9bb0;">#{{ $incident->id }}</td>
                <td><a href="{{ route('incidents.show', $incident) }}">{{ Str::limit($incident->title, 45) }}</a></td>
                <td><span class="badge badge-{{ $incident->severity }}">{{ $incident->severity }}</span></td>
                <td><span class="badge badge-{{ $incident->status }}">{{ $incident->status }}</span></td>
                <td style="font-size: 0.8rem;">{{ $incident->affected_host }}</td>
                <td>{{ $incident->assignee?->name ?? '—' }}</td>
                <td style="color: #8b9bb0; font-size: 0.8rem;">{{ $incident->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align: center; color: #8b9bb0; padding: 2rem;">No se encontraron incidentes</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($incidents->hasPages())
<div class="pagination">
    @if($incidents->onFirstPage())
        <span>← Anterior</span>
    @else
        <a href="{{ $incidents->previousPageUrl() }}">← Anterior</a>
    @endif
    <span class="active">{{ $incidents->currentPage() }} / {{ $incidents->lastPage() }}</span>
    @if($incidents->hasMorePages())
        <a href="{{ $incidents->nextPageUrl() }}">Siguiente →</a>
    @else
        <span>Siguiente →</span>
    @endif
</div>
@endif
@endsection
