<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0b0f14;
            --surface: #121820;
            --surface-2: #1a2330;
            --border: #2a3544;
            --text: #e8edf4;
            --muted: #8b9bb0;
            --accent: #3b82f6;
            --accent-hover: #2563eb;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --critical: #dc2626;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'IBM Plex Sans', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.5;
        }
        a { color: var(--accent); text-decoration: none; }
        a:hover { color: #60a5fa; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar {
            width: 240px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }
        .brand {
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: -0.02em;
            margin-bottom: 0.25rem;
        }
        .brand span { color: var(--accent); }
        .brand-sub { font-size: 0.7rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 2rem; }
        .nav { list-style: none; flex: 1; }
        .nav a {
            display: block;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            color: var(--muted);
            font-weight: 500;
            font-size: 0.9rem;
        }
        .nav a:hover, .nav a.active { background: var(--surface-2); color: var(--text); }
        .nav a.active { border-left: 3px solid var(--accent); padding-left: calc(0.85rem - 3px); }
        .user-box {
            padding-top: 1rem;
            border-top: 1px solid var(--border);
            font-size: 0.8rem;
        }
        .user-box strong { display: block; font-size: 0.85rem; }
        .role-badge {
            display: inline-block;
            margin-top: 0.35rem;
            padding: 0.15rem 0.5rem;
            border-radius: 4px;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: var(--surface-2);
            color: var(--muted);
        }
        .main { flex: 1; padding: 2rem; overflow-x: auto; }
        .page-header { margin-bottom: 1.75rem; }
        .page-header h1 { font-size: 1.5rem; font-weight: 600; }
        .page-header p { color: var(--muted); font-size: 0.9rem; margin-top: 0.25rem; }
        .alert {
            padding: 0.85rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
            border: 1px solid var(--border);
        }
        .alert-success { background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.3); color: #86efac; }
        .alert-error { background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); color: #fff; }
        .btn-ghost { background: transparent; color: var(--muted); border: 1px solid var(--border); }
        .btn-ghost:hover { color: var(--text); border-color: var(--muted); }
        .btn-danger { background: rgba(239, 68, 68, 0.15); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.3); }
        .badge {
            display: inline-block;
            padding: 0.2rem 0.55rem;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .badge-critical { background: rgba(220, 38, 38, 0.2); color: #fca5a5; }
        .badge-high { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
        .badge-medium { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
        .badge-low { background: rgba(34, 197, 94, 0.15); color: #86efac; }
        .badge-open { background: rgba(59, 130, 246, 0.15); color: #93c5fd; }
        .badge-investigating { background: rgba(245, 158, 11, 0.15); color: #fcd34d; }
        .badge-resolved { background: rgba(34, 197, 94, 0.15); color: #86efac; }
        .badge-closed { background: rgba(139, 155, 176, 0.2); color: var(--muted); }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        th, td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        th { color: var(--muted); font-weight: 500; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
        tr:hover td { background: rgba(255,255,255,0.02); }
        .form-group { margin-bottom: 1.1rem; }
        label { display: block; font-size: 0.8rem; font-weight: 500; color: var(--muted); margin-bottom: 0.35rem; }
        input, select, textarea {
            width: 100%;
            padding: 0.6rem 0.75rem;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-family: inherit;
            font-size: 0.9rem;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .error-text { color: #fca5a5; font-size: 0.8rem; margin-top: 0.25rem; }
        .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.75rem; }
        .stat-card h3 { font-size: 1.75rem; font-weight: 700; font-family: 'JetBrains Mono', monospace; }
        .stat-card p { color: var(--muted); font-size: 0.8rem; margin-top: 0.25rem; }
        .toolbar { display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
        .filters { display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; }
        .filters select, .filters input { width: auto; min-width: 140px; }
        .pagination { display: flex; gap: 0.5rem; margin-top: 1.25rem; flex-wrap: wrap; }
        .pagination a, .pagination span {
            padding: 0.4rem 0.75rem;
            border-radius: 6px;
            border: 1px solid var(--border);
            font-size: 0.85rem;
            color: var(--muted);
        }
        .pagination .active { background: var(--accent); color: #fff; border-color: var(--accent); }
        @media (max-width: 768px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--border); }
            .main { padding: 1.25rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
@auth
<div class="layout">
    <aside class="sidebar">
        <div class="brand">Sentinel<span>Ops</span></div>
        <div class="brand-sub">Security Operations</div>
        <ul class="nav">
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('incidents.index') }}" class="{{ request()->routeIs('incidents.*') ? 'active' : '' }}">Incidentes</a></li>
            @if(auth()->user()->isAdmin())
            <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Usuarios</a></li>
            @endif
        </ul>
        <div class="user-box">
            <strong>{{ auth()->user()->name }}</strong>
            <span class="role-badge">{{ auth()->user()->role }}</span>
            <form action="{{ route('logout') }}" method="POST" style="margin-top: 0.75rem;">
                @csrf
                <button type="submit" class="btn btn-ghost" style="width: 100%;">Cerrar sesión</button>
            </form>
        </div>
    </aside>
    <main class="main">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</div>
@else
    @yield('content')
@endauth
</body>
</html>
