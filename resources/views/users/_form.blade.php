@php
    $user = $user ?? null;
@endphp

<div class="form-group">
    <label for="name">Nombre</label>
    <input type="text" id="name" name="name" value="{{ old('name', $user?->name) }}" required>
    @error('name')<p class="error-text">{{ $message }}</p>@enderror
</div>

<div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="{{ old('email', $user?->email) }}" required>
    @error('email')<p class="error-text">{{ $message }}</p>@enderror
</div>

<div class="form-group">
    <label for="role">Rol</label>
    <select id="role" name="role" required>
        @foreach(['admin' => 'Administrador', 'analyst' => 'Analista SOC', 'viewer' => 'Visor'] as $value => $label)
            <option value="{{ $value }}" @selected(old('role', $user?->role) === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('role')<p class="error-text">{{ $message }}</p>@enderror
</div>

<div class="form-group">
    <label for="password">{{ $user ? 'Nueva contraseña (opcional)' : 'Contraseña' }}</label>
    <input type="password" id="password" name="password" {{ $user ? '' : 'required' }} minlength="8" autocomplete="new-password">
    @error('password')<p class="error-text">{{ $message }}</p>@enderror
</div>

<div class="form-group">
    <label for="password_confirmation">Confirmar contraseña</label>
    <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" autocomplete="new-password">
</div>

<div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" id="is_active" name="is_active" value="1" style="width: auto;" @checked(old('is_active', $user?->is_active ?? true))>
    <label for="is_active" style="margin: 0;">Usuario activo</label>
</div>
