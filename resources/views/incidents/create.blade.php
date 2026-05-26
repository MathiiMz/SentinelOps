@extends('layouts.app')

@section('title', 'Nuevo incidente')

@section('content')
<div class="page-header">
    <h1>Nuevo incidente</h1>
    <p>Registrar una nueva alerta de seguridad</p>
</div>

<div class="card" style="max-width: 640px;">
    <form action="{{ route('incidents.store') }}" method="POST">
        @csrf
        @include('incidents._form')
        <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
            <button type="submit" class="btn btn-primary">Crear incidente</button>
            <a href="{{ route('incidents.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection
