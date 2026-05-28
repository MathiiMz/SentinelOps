<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Incidente #{{ $incident->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { margin: 0 0 6px; font-size: 22px; }
        h2 { margin: 20px 0 8px; font-size: 14px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .meta { color: #6b7280; margin-bottom: 18px; }
        .badge { display: inline-block; border: 1px solid #9ca3af; border-radius: 4px; padding: 2px 8px; margin-right: 6px; font-size: 11px; text-transform: uppercase; }
        .box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; margin-bottom: 10px; }
        .muted { color: #6b7280; font-size: 11px; }
        .timeline-item { border-bottom: 1px solid #f3f4f6; padding: 8px 0; }
    </style>
</head>
<body>
    <h1>Incidente #{{ $incident->id }} - {{ $incident->title }}</h1>
    <p class="meta">Exportado: {{ now()->format('d/m/Y H:i') }}</p>

    <div>
        <span class="badge">{{ $incident->severity }}</span>
        <span class="badge">{{ $incident->status }}</span>
    </div>

    <h2>Detalle</h2>
    <div class="box">
        <p><strong>Creado por:</strong> {{ $incident->creator->name }}</p>
        <p><strong>Asignado a:</strong> {{ $incident->assignee?->name ?? 'Sin asignar' }}</p>
        <p><strong>IP de origen:</strong> {{ $incident->source_ip }}</p>
        <p><strong>Host afectado:</strong> {{ $incident->affected_host }}</p>
        <p><strong>Creado:</strong> {{ $incident->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <h2>Descripcion</h2>
    <div class="box">
        {{ $incident->description }}
    </div>

    <h2>Timeline</h2>
    <div class="box">
        @forelse($timeline as $item)
            <div class="timeline-item">
                <p>{{ $item['line'] }}</p>
                <p class="muted">{{ $item['created_at']->format('d/m/Y H:i') }}</p>
            </div>
        @empty
            <p class="muted">No hay actividad registrada.</p>
        @endforelse
    </div>
</body>
</html>
