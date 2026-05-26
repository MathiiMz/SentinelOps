@extends('layouts.app')

@section('title', 'Incidente #'.$incident->id)

@section('content')
<div class="page-header" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 1rem;">
    <div>
        <p style="color: #8b9bb0; font-size: 0.8rem; font-family: 'JetBrains Mono', monospace;">#{{ $incident->id }}</p>
        <h1>{{ $incident->title }}</h1>
        <p style="margin-top: 0.5rem;">
            <span class="badge badge-{{ $incident->severity }}">{{ $incident->severity }}</span>
            <span class="badge badge-{{ $incident->status }}">{{ $incident->status }}</span>
        </p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        @if(auth()->user()->isAdmin() || auth()->user()->id === $incident->created_by)
            <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-ghost">Editar</a>
        @endif
        @if(auth()->user()->isAdmin())
            <form action="{{ route('incidents.destroy', $incident) }}" method="POST" onsubmit="return confirm('¿Eliminar este incidente?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </form>
        @endif
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 320px; gap: 1.25rem; align-items: start;">
    <div>
        <div class="card" style="margin-bottom: 1.25rem;">
            <h2 style="font-size: 0.85rem; color: #8b9bb0; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">Descripción</h2>
            <p style="white-space: pre-wrap;">{{ $incident->description }}</p>
        </div>

        <div class="card">
            <h2 style="font-size: 0.85rem; color: #8b9bb0; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">Comentarios ({{ $incident->comments->count() }})</h2>
            @forelse($incident->comments->sortByDesc('created_at') as $comment)
                <div style="padding: 0.85rem 0; border-bottom: 1px solid #2a3544;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                        <strong style="font-size: 0.85rem;">{{ $comment->user->name }}</strong>
                        <span style="color: #8b9bb0; font-size: 0.75rem;">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <p style="font-size: 0.9rem; white-space: pre-wrap;">{{ $comment->content }}</p>
                </div>
            @empty
                <p style="color: #8b9bb0; font-size: 0.9rem;">Sin comentarios aún.</p>
            @endforelse

            @if(!auth()->user()->isViewer())
            <form action="{{ route('incidents.comments.store', $incident) }}" method="POST" style="margin-top: 1.25rem;">
                @csrf
                <div class="form-group">
                    <label for="content">Agregar comentario</label>
                    <textarea id="content" name="content" rows="3" required>{{ old('content') }}</textarea>
                    @error('content')<p class="error-text">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn btn-primary">Publicar</button>
            </form>
            @endif
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom: 1rem;">
            <h2 style="font-size: 0.85rem; color: #8b9bb0; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">Detalles</h2>
            <dl style="font-size: 0.875rem;">
                <dt style="color: #8b9bb0; margin-bottom: 0.2rem;">IP origen</dt>
                <dd style="font-family: 'JetBrains Mono', monospace; margin-bottom: 0.75rem;">{{ $incident->source_ip }}</dd>
                <dt style="color: #8b9bb0; margin-bottom: 0.2rem;">Host afectado</dt>
                <dd style="margin-bottom: 0.75rem;">{{ $incident->affected_host }}</dd>
                <dt style="color: #8b9bb0; margin-bottom: 0.2rem;">Creado por</dt>
                <dd style="margin-bottom: 0.75rem;">{{ $incident->creator->name }}</dd>
                <dt style="color: #8b9bb0; margin-bottom: 0.2rem;">Asignado a</dt>
                <dd style="margin-bottom: 0.75rem;">{{ $incident->assignee?->name ?? 'Sin asignar' }}</dd>
                <dt style="color: #8b9bb0; margin-bottom: 0.2rem;">Creado</dt>
                <dd>{{ $incident->created_at->format('d/m/Y H:i') }}</dd>
            </dl>
        </div>

        @if(!auth()->user()->isViewer())
        <div class="card">
            <h2 style="font-size: 0.85rem; color: #8b9bb0; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">Cambiar estado</h2>
            <form action="{{ route('incidents.status', $incident) }}" method="POST">
                @csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()">
                    @foreach(['open','investigating','resolved','closed'] as $s)
                        <option value="{{ $s }}" @selected($incident->status === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        @endif
    </div>
</div>

<a href="{{ route('incidents.index') }}" class="btn btn-ghost" style="margin-top: 1.25rem;">← Volver al listado</a>
@endsection
