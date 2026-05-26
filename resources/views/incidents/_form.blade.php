@php
    $incident = $incident ?? null;
    $edit = $edit ?? false;
@endphp

<div class="form-group">
    <label for="title">Título</label>
    <input type="text" id="title" name="title" value="{{ old('title', $incident?->title) }}" required>
    @error('title')<p class="error-text">{{ $message }}</p>@enderror
</div>

<div class="form-group">
    <label for="description">Descripción</label>
    <textarea id="description" name="description" rows="4" required>{{ old('description', $incident?->description) }}</textarea>
    @error('description')<p class="error-text">{{ $message }}</p>@enderror
</div>

<div class="form-group">
    <label for="severity">Severidad</label>
    <select id="severity" name="severity" required>
        @foreach(['critical','high','medium','low'] as $s)
            <option value="{{ $s }}" @selected(old('severity', $incident?->severity) === $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    @error('severity')<p class="error-text">{{ $message }}</p>@enderror
</div>

@if($edit)
<div class="form-group">
    <label for="status">Estado</label>
    <select id="status" name="status" required>
        @foreach(['open','investigating','resolved','closed'] as $s)
            <option value="{{ $s }}" @selected(old('status', $incident?->status) === $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    @error('status')<p class="error-text">{{ $message }}</p>@enderror
</div>
@endif

<div class="form-group">
    <label for="source_ip">IP de origen</label>
    <input type="text" id="source_ip" name="source_ip" value="{{ old('source_ip', $incident?->source_ip) }}" required placeholder="192.168.1.1">
    @error('source_ip')<p class="error-text">{{ $message }}</p>@enderror
</div>

<div class="form-group">
    <label for="affected_host">Host afectado</label>
    <input type="text" id="affected_host" name="affected_host" value="{{ old('affected_host', $incident?->affected_host) }}" required placeholder="server-01.local">
    @error('affected_host')<p class="error-text">{{ $message }}</p>@enderror
</div>

<div class="form-group">
    <label for="assigned_to">Asignar a (opcional)</label>
    <select id="assigned_to" name="assigned_to">
        <option value="">Sin asignar</option>
        @foreach($analysts as $analyst)
            <option value="{{ $analyst->id }}" @selected(old('assigned_to', $incident?->assigned_to) == $analyst->id)>
                {{ $analyst->name }} ({{ $analyst->role }})
            </option>
        @endforeach
    </select>
    @error('assigned_to')<p class="error-text">{{ $message }}</p>@enderror
</div>
