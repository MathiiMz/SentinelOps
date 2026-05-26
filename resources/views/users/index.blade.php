@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
<div class="page-header">
    <h1>Gestión de usuarios</h1>
    <p>Administración de cuentas y roles del SOC</p>
</div>

<div class="toolbar">
    <form class="filters" method="GET" action="{{ route('admin.users.index') }}">
        <input type="search" name="q" placeholder="Buscar nombre o email..." value="{{ request('q') }}">
        <select name="role">
            <option value="">Todos los roles</option>
            @foreach(['admin','analyst','viewer'] as $r)
                <option value="{{ $r }}" @selected(request('role') === $r)>{{ $r }}</option>
            @endforeach
        </select>
        <select name="is_active">
            <option value="">Activo / inactivo</option>
            <option value="1" @selected(request('is_active') === '1')>Activos</option>
            <option value="0" @selected(request('is_active') === '0')>Inactivos</option>
        </select>
        <button type="submit" class="btn btn-ghost">Filtrar</button>
        @if(request()->hasAny(['q','role','is_active']))
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Limpiar</a>
        @endif
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Nuevo usuario</a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Registro</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td style="font-family: 'JetBrains Mono', monospace; color: #8b9bb0;">#{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td style="font-size: 0.85rem;">{{ $user->email }}</td>
                <td><span class="role-badge">{{ $user->role }}</span></td>
                <td>
                    @if($user->is_active)
                        <span class="badge badge-resolved">activo</span>
                    @else
                        <span class="badge badge-closed">inactivo</span>
                    @endif
                </td>
                <td style="color: #8b9bb0; font-size: 0.8rem;">{{ $user->created_at->format('d/m/Y') }}</td>
                <td style="white-space: nowrap;">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost" style="padding: 0.35rem 0.65rem;">Editar</a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar a {{ $user->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.65rem;">Eliminar</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align: center; color: #8b9bb0; padding: 2rem;">No hay usuarios</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($users->hasPages())
<div class="pagination">
    @if($users->onFirstPage())
        <span>← Anterior</span>
    @else
        <a href="{{ $users->previousPageUrl() }}">← Anterior</a>
    @endif
    <span class="active">{{ $users->currentPage() }} / {{ $users->lastPage() }}</span>
    @if($users->hasMorePages())
        <a href="{{ $users->nextPageUrl() }}">Siguiente →</a>
    @else
        <span>Siguiente →</span>
    @endif
</div>
@endif
@endsection
