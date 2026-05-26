@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<style>
    .auth-wrap {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background:
            radial-gradient(ellipse at 20% 50%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 20%, rgba(34, 197, 94, 0.05) 0%, transparent 40%),
            #0b0f14;
    }
    .auth-card {
        width: 100%;
        max-width: 400px;
        background: #121820;
        border: 1px solid #2a3544;
        border-radius: 16px;
        padding: 2rem;
    }
    .auth-card h1 { font-size: 1.5rem; margin-bottom: 0.25rem; }
    .auth-card .sub { color: #8b9bb0; font-size: 0.85rem; margin-bottom: 1.75rem; }
    .auth-brand { font-weight: 700; font-size: 1.25rem; margin-bottom: 1.5rem; }
    .auth-brand span { color: #3b82f6; }
    .demo-hint {
        margin-top: 1.5rem;
        padding: 0.85rem;
        background: #1a2330;
        border-radius: 8px;
        font-size: 0.75rem;
        color: #8b9bb0;
        line-height: 1.6;
    }
    .demo-hint code { font-family: 'JetBrains Mono', monospace; color: #93c5fd; font-size: 0.7rem; }
</style>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-brand">Sentinel<span>Ops</span></div>
        <h1>Iniciar sesión</h1>
        <p class="sub">Plataforma de gestión de incidentes SOC</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')<p class="error-text">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" id="remember" name="remember" style="width: auto;">
                <label for="remember" style="margin: 0;">Recordarme</label>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">Entrar</button>
        </form>

        <div class="demo-hint">
            <strong>Cuentas de prueba</strong><br>
            Admin: <code>admin@sentinelops.com</code><br>
            Contraseña: <code>AdminPassword123!</code>
        </div>
    </div>
</div>
@endsection
