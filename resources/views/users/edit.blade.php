@extends('layouts.app')

@section('title', 'Editar usuario')

@section('content')
<div class="page-header">
    <h1>Editar usuario</h1>
    <p>{{ $user->email }}</p>
</div>

<div class="card" style="max-width: 520px;">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf @method('PUT')
        @include('users._form', ['user' => $user])
        <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection
