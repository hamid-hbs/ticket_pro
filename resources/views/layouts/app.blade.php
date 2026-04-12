<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <style>
        :root {
            --bg: #f5f7fb;
            --surface: rgba(255, 255, 255, 0.94);
            --surface-alt: #f8fafc;
            --text: #0f172a;
            --muted: #64748b;
            --accent: #2563eb;
            --accent-strong: #1d4ed8;
            --border: rgba(15, 23, 42, 0.08);
            --ok: #16a34a;
            --ok-soft: rgba(22, 163, 74, 0.12);
            --err: #dc2626;
            --err-soft: rgba(220, 38, 38, 0.1);
            --shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        }
        * { box-sizing: border-box; }
        html { min-height: 100%; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            line-height: 1.6;
            background:
                radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 28%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(245, 247, 251, 0.96)),
                var(--bg);
        }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .container {
            width: min(1120px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 2rem 0 3rem;
        }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
        }
        .card--narrow { max-width: 480px; margin: 0 auto; }
        .page-note { color: var(--muted); font-size: 0.95rem; margin-bottom: 1.25rem; }
        h1 { font-size: 1.65rem; margin: 0 0 0.85rem; font-weight: 700; letter-spacing: -0.02em; }
        label { display: block; font-size: 0.875rem; font-weight: 600; color: var(--text); margin-bottom: 0.35rem; }
        input, select, button { font: inherit; }
        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            border: 1px solid rgba(15, 23, 42, 0.12);
            background: #fff;
            color: var(--text);
            transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
        }
        input:focus, select:focus {
            outline: none;
            border-color: rgba(37, 99, 235, 0.45);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }
        button, .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.85rem 1.15rem;
            border-radius: 12px;
            border: 1px solid transparent;
            background: linear-gradient(180deg, var(--accent), var(--accent-strong));
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.18);
            transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
        }
        button:hover, .btn:hover { transform: translateY(-1px); box-shadow: 0 12px 24px rgba(37, 99, 235, 0.22); }
        button.secondary, .btn.secondary {
            background: #fff;
            color: var(--text);
            border-color: rgba(15, 23, 42, 0.12);
            box-shadow: none;
        }
        button.secondary:hover, .btn.secondary:hover { background: var(--surface-alt); }
        button.danger, .btn.danger {
            background: linear-gradient(180deg, #ef4444, #dc2626);
            color: #fff;
            box-shadow: 0 10px 20px rgba(220, 38, 38, 0.18);
        }
        button.danger:hover, .btn.danger:hover {
            box-shadow: 0 12px 24px rgba(220, 38, 38, 0.22);
            background: linear-gradient(180deg, #f87171, #ef4444);
        }
        .error { color: var(--err); font-size: 0.875rem; margin-top: 0.3rem; }
        .flash-ok {
            background: var(--ok-soft);
            border: 1px solid rgba(22, 163, 74, 0.22);
            color: #166534;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        .flash-err {
            background: var(--err-soft);
            border: 1px solid rgba(220, 38, 38, 0.22);
            color: #991b1b;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        table { width: 100%; border-collapse: collapse; font-size: 0.95rem; }
        th, td { text-align: left; padding: 0.8rem 0.7rem; border-bottom: 1px solid rgba(15, 23, 42, 0.08); }
        th { color: var(--muted); font-weight: 600; }
        .badge { display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; }
        .badge-paid { background: rgba(22, 163, 74, 0.12); color: #166534; }
        .badge-used { background: rgba(100, 116, 139, 0.14); color: #475569; }
        .badge-admin { background: rgba(37, 99, 235, 0.12); color: #1d4ed8; }
        .badge-superadmin { background: rgba(124, 58, 237, 0.12); color: #6d28d9; }
        .badge-user { background: rgba(100, 116, 139, 0.12); color: #475569; }
        nav {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 0.9rem 1rem;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }
        nav a {
            color: var(--muted);
            font-weight: 600;
            padding: 0.55rem 0.9rem;
            border-radius: 999px;
        }
        nav a:hover {
            color: var(--text);
            background: rgba(37, 99, 235, 0.08);
            text-decoration: none;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05);
        }
        .stat strong { display: block; font-size: 1.75rem; color: var(--accent); }
        .stat span { font-size: 0.8rem; color: var(--muted); }
        .muted { color: var(--muted); }
        .form-group { margin-bottom: 1rem; }
        .actions { display: flex; flex-wrap: wrap; gap: 0.75rem; }
        .mt-large { margin-top: 3rem; }
        .mb-0 { margin-bottom: 0; }
        .flex-spacer { flex: 1; }
        .cursor-pointer { cursor: pointer; }
        .check-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .check-row span { color: var(--muted); font-size: 0.875rem; }
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            padding: 1rem;
            margin: 1rem 0 1.25rem;
            background: var(--surface-alt);
            border: 1px solid var(--border);
            border-radius: 16px;
        }
        .summary strong { color: var(--text); }
        .pagination-wrap {
            margin-top: 1.25rem;
            display: flex;
            justify-content: center;
        }
        .pagination-wrap nav[role="navigation"] {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--border);
            border-radius: 999px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        }
        .pagination-wrap nav[role="navigation"] > div {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        .pagination-wrap nav[role="navigation"] a,
        .pagination-wrap nav[role="navigation"] span[aria-disabled="true"],
        .pagination-wrap nav[role="navigation"] span[aria-current="page"] {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.45rem;
            height: 2.45rem;
            padding: 0 0.8rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.15s ease, background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }
        .pagination-wrap nav[role="navigation"] a {
            color: var(--text);
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.1);
        }
        .pagination-wrap nav[role="navigation"] a:hover {
            background: rgba(37, 99, 235, 0.08);
            border-color: rgba(37, 99, 235, 0.2);
            transform: translateY(-1px);
            text-decoration: none;
        }
        .pagination-wrap nav[role="navigation"] span[aria-disabled="true"] {
            color: rgba(100, 116, 139, 0.7);
            background: rgba(248, 250, 252, 0.8);
            border: 1px solid rgba(15, 23, 42, 0.08);
            cursor: not-allowed;
        }
        .pagination-wrap nav[role="navigation"] span[aria-current="page"] {
            color: #fff;
            background: linear-gradient(180deg, var(--accent), var(--accent-strong));
            border: 1px solid transparent;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.18);
        }
        .pagination-wrap nav[role="navigation"] svg {
            width: 1rem;
            height: 1rem;
        }
        .pagination-wrap nav[role="navigation"] .hidden {
            display: none;
        }
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
