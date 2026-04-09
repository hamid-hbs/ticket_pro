<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <style>
        :root { --bg: #0f172a; --card: #1e293b; --text: #f8fafc; --muted: #94a3b8; --accent: #38bdf8; --ok: #22c55e; --err: #ef4444; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; background: var(--bg); color: var(--text); line-height: 1.5; min-height: 100vh; }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .container { max-width: 960px; margin: 0 auto; padding: 1.5rem; }
        .card { background: var(--card); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 24px rgba(0,0,0,.35); }
        h1 { font-size: 1.5rem; margin: 0 0 1rem; font-weight: 600; }
        label { display: block; font-size: 0.875rem; color: var(--muted); margin-bottom: 0.35rem; }
        input, select, button { font: inherit; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; max-width: 420px; padding: 0.6rem 0.75rem; border-radius: 8px; border: 1px solid #334155; background: #0f172a; color: var(--text); }
        button, .btn { display: inline-block; padding: 0.55rem 1rem; border-radius: 8px; border: none; background: var(--accent); color: #0f172a; font-weight: 600; cursor: pointer; }
        button.secondary, .btn.secondary { background: #334155; color: var(--text); }
        .error { color: var(--err); font-size: 0.875rem; margin-top: 0.25rem; }
        .flash-ok { background: rgba(34,197,94,.15); border: 1px solid var(--ok); color: #86efac; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .flash-err { background: rgba(239,68,68,.12); border: 1px solid var(--err); color: #fca5a5; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th, td { text-align: left; padding: 0.6rem 0.5rem; border-bottom: 1px solid #334155; }
        th { color: var(--muted); font-weight: 500; }
        .badge { display: inline-block; padding: 0.15rem 0.5rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-paid { background: rgba(34,197,94,.2); color: #86efac; }
        .badge-pending { background: rgba(234,179,8,.2); color: #fde047; }
        .badge-used { background: rgba(148,163,184,.2); color: #cbd5e1; }
        nav { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #334155; }
        nav a { color: var(--muted); }
        nav a:hover { color: var(--accent); }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .stat { background: var(--card); border-radius: 10px; padding: 1rem; text-align: center; }
        .stat strong { display: block; font-size: 1.75rem; color: var(--accent); }
        .stat span { font-size: 0.8rem; color: var(--muted); }
    </style>
    @stack('head')
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
