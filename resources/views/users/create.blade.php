@extends('layouts.app')

@section('title', 'Nuevo usuario')

@section('content')
<div class="page-header">
    <h1>Nuevo usuario</h1>
    <p>Crear cuenta para el equipo SOC</p>
</div>

<div class="card" style="max-width: 520px;">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        @include('users._form')
        <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
            <button type="submit" class="btn btn-primary">Crear usuario</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection
