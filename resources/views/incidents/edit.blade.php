@extends('layouts.app')

@section('title', 'Editar incidente')

@section('content')
<div class="page-header">
    <h1>Editar incidente #{{ $incident->id }}</h1>
    <p>{{ $incident->title }}</p>
</div>

<div class="card" style="max-width: 640px;">
    <form action="{{ route('incidents.update', $incident) }}" method="POST">
        @csrf @method('PUT')
        @include('incidents._form', ['incident' => $incident, 'edit' => true])
        <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection
