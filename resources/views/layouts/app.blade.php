<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* ══════════════════════════════════════════
           DESIGN TOKENS  — Palette Affiche IG 9.0's
           Teal · Orange · Chocolat · Crème
           ══════════════════════════════════════════ */
        :root {
            /* Couleurs primaires */
            --teal:          #00897B;   /* teal principal */
            --teal-dark:     #006B63;   /* teal foncé */
            --teal-deeper:   #00514A;   /* teal très foncé */
            --teal-light:    #E0F5F3;   /* teal très clair */
            --teal-soft:     rgba(0,137,123,0.1);
            --teal-border:   rgba(0,137,123,0.22);

            /* Accent orange */
            --orange:        #E07212;
            --orange-dark:   #B85A09;
            --orange-soft:   rgba(224,114,18,0.1);
            --orange-border: rgba(224,114,18,0.22);

            /* Chocolat sombre (titres, fonds profonds) */
            --choco:         #2C1507;
            --choco-soft:    #4A2B15;

            /* Crème / fond */
            --cream:         #F5EDD0;
            --cream-dark:    #EDE0C0;

            /* Fond page */
            --bg:            #F3FAF9;
            --surface:       #ffffff;
            --surface-alt:   #F7FDFB;

            /* Texte */
            --text:          #0D2B27;
            --text-2:        #2D5C54;
            --muted:         #6B9690;

            /* Statuts */
            --ok:            #059669;
            --ok-soft:       rgba(5,150,105,0.1);
            --err:           #DC2626;
            --err-soft:      rgba(220,38,38,0.08);
            --warn:          var(--orange);
            --warn-soft:     var(--orange-soft);

            /* Ombre / bordure */
            --border:        rgba(0,107,99,0.1);
            --shadow-sm:     0 2px 8px rgba(0,81,74,0.07);
            --shadow-md:     0 8px 28px rgba(0,81,74,0.11);
            --shadow-lg:     0 20px 52px rgba(0,81,74,0.15);

            /* Gradients */
            --grad-teal:     linear-gradient(135deg, var(--teal-dark) 0%, var(--teal) 100%);
            --grad-orange:   linear-gradient(135deg, var(--orange-dark) 0%, var(--orange) 100%);
            --grad-hero:     linear-gradient(135deg, var(--teal-deeper) 0%, var(--teal-dark) 50%, var(--teal) 100%);

            --radius:    16px;
            --radius-sm: 10px;
        }

        /* ── Reset ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { min-height: 100%; scroll-behavior: smooth; }
        body {
            min-height: 100vh;
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
            font-size: 15px;
            color: var(--text);
            line-height: 1.65;
            background:
                radial-gradient(ellipse 70% 55% at 80% -5%, rgba(0,137,123,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 50% 45% at -5% 90%, rgba(0,107,99,0.09) 0%, transparent 55%),
                var(--bg);
        }
        a { color: var(--teal); text-decoration: none; }
        a:hover { text-decoration: underline; color: var(--teal-dark); }
        img { display: block; max-width: 100%; }

        /* ── Layout ── */
        .container {
            width: min(1200px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 1.5rem 0 4rem;
        }

        /* ── Topbar ── */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 0.7rem 1rem 0.7rem 1.1rem;
            border-radius: 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(14px);
            position: sticky;
            top: 1rem;
            z-index: 50;
        }

        /* Brand */
        .brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            text-decoration: none !important;
            flex: none;
        }
        .brand-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: var(--grad-teal);
            display: flex;
            align-items: center;
            justify-content: center;
            flex: none;
            box-shadow: 0 4px 12px rgba(0,137,123,0.4);
        }
        .brand-icon svg { width: 20px; height: 20px; color: #fff; }
        .brand-text { display: flex; flex-direction: column; }
        .brand-text strong { font-size: 0.92rem; font-weight: 800; color: var(--text); letter-spacing: -0.02em; }
        .brand-text span   { font-size: 0.72rem; color: var(--muted); font-weight: 500; }

        /* Nav */
        .topbar nav {
            display: flex;
            flex-wrap: wrap;
            gap: 0.3rem;
            align-items: center;
        }
        .topbar nav a {
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.42rem 0.85rem;
            border-radius: 999px;
            border: 1px solid transparent;
            transition: color .15s, background .15s, border-color .15s;
        }
        .topbar nav a:hover {
            color: var(--teal-dark);
            background: var(--teal-soft);
            text-decoration: none;
        }
        .topbar nav a.active {
            color: var(--teal-dark);
            background: var(--teal-soft);
            border-color: var(--teal-border);
        }

        /* ── Back button (per-page) ── */
        .page-back {
            margin-bottom: 1.25rem;
        }
        .back-btn {
            appearance: none;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.42rem 0.9rem 0.42rem 0.65rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--teal-dark);
            background: var(--teal-soft);
            border: 1px solid var(--teal-border);
            box-shadow: none;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s, transform .15s;
        }
        .back-btn:hover {
            background: rgba(0,137,123,0.15);
            transform: translateX(-2px);
            text-decoration: none;
            color: var(--teal-dark);
        }
        .back-btn svg { width: 15px; height: 15px; flex: none; }

        .nav-cta {
            color: var(--teal-dark) !important;
            background: var(--teal-soft) !important;
            border-color: var(--teal-border) !important;
        }
        .nav-cta:hover { background: rgba(0,137,123,0.15) !important; }

        .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.42rem 0.85rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #fff;
            background: #EF4444;
            border: 1px solid #EF4444;
            cursor: pointer;
            font-family: inherit;
            transition: background .15s, opacity .15s;
        }
        .logout-btn:hover { background: #DC2626; opacity: 0.95; }

        /* ── Cards ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
        }
        .card--narrow { max-width: 480px; margin: 0 auto; }

        /* ── Typography ── */
        h1 { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.03em; margin: 0 0 0.75rem; color: var(--text); }
        h2 { font-size: 1.15rem; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 0.5rem; }
        .page-note { color: var(--muted); font-size: 0.9rem; margin-bottom: 1.25rem; }
        .muted { color: var(--muted); }

        /* ── Forms ── */
        label { display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-2); margin-bottom: 0.3rem; letter-spacing: 0.01em; text-transform: uppercase; }
        input, select, button, textarea { font: inherit; }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        input[type="time"],
        select,
        textarea {
            width: 100%;
            padding: 0.72rem 1rem;
            border-radius: var(--radius-sm);
            border: 1.5px solid rgba(0,107,99,0.15);
            background: #fff;
            color: var(--text);
            transition: border-color .15s, box-shadow .15s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--teal);
            box-shadow: 0 0 0 4px rgba(0,137,123,0.12);
        }
        .form-group { margin-bottom: 1.1rem; }
        .error { color: var(--err); font-size: 0.82rem; margin-top: 0.3rem; }

        /* ── Buttons ── */
        button, .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.7rem 1.2rem;
            border-radius: var(--radius-sm);
            border: 1px solid transparent;
            background: var(--grad-teal);
            color: #fff;
            font-weight: 700;
            font-size: 0.84rem;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(0,107,99,0.3);
            transition: transform .15s, box-shadow .15s, opacity .15s;
            letter-spacing: 0.01em;
        }
        button:hover, .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(0,107,99,0.38);
            text-decoration: none;
            color: #fff;
        }
        button:active, .btn:active { transform: translateY(0); }

        button.secondary, .btn.secondary {
            background: var(--teal-soft);
            color: var(--teal-dark);
            border-color: var(--teal-border);
            box-shadow: none;
        }
        button.secondary:hover, .btn.secondary:hover {
            background: rgba(0,137,123,0.15);
            box-shadow: none;
            color: var(--teal-dark);
        }

        button.warning, .btn.warning {
            background: var(--grad-orange);
            box-shadow: 0 4px 14px rgba(224,114,18,0.28);
        }
        button.warning:hover, .btn.warning:hover {
            box-shadow: 0 8px 22px rgba(224,114,18,0.38);
        }

        button.danger, .btn.danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 14px rgba(220,38,38,0.25);
        }
        button.danger:hover, .btn.danger:hover { box-shadow: 0 8px 22px rgba(220,38,38,0.35); }

        /* ── Flash messages ── */
        .flash-ok {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            background: var(--ok-soft);
            border: 1px solid rgba(5,150,105,0.22);
            color: #064e3b;
            padding: 0.85rem 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.25rem;
            font-size: 0.88rem;
            font-weight: 500;
        }
        .flash-err {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            background: var(--err-soft);
            border: 1px solid rgba(220,38,38,0.22);
            color: #7f1d1d;
            padding: 0.85rem 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.25rem;
            font-size: 0.88rem;
            font-weight: 500;
        }

        /* ── Badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.22rem;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .badge-paid      { background: rgba(5,150,105,0.12); color: #065f46; border: 1px solid rgba(5,150,105,0.22); }
        .badge-used      { background: rgba(100,116,139,0.1); color: #475569; border: 1px solid rgba(100,116,139,0.18); }
        .badge-admin     { background: var(--teal-soft); color: var(--teal-dark); border: 1px solid var(--teal-border); }
        .badge-superadmin { background: var(--orange-soft); color: var(--orange-dark); border: 1px solid var(--orange-border); }
        .badge-user      { background: rgba(100,116,139,0.08); color: #64748b; border: 1px solid rgba(100,116,139,0.15); }

        /* ── Stats grid ── */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.75rem;
        }
        .stat {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem 1rem;
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: transform .2s, box-shadow .2s;
        }
        .stat:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
        .stat strong {
            display: block;
            font-size: 2rem;
            font-weight: 900;
            background: var(--grad-teal);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.1;
            margin-bottom: 0.2rem;
        }
        .stat span { font-size: 0.74rem; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }

        /* ── Icon action buttons ── */
        .action-icon-group { display: inline-flex; align-items: center; gap: 0.4rem; }
        .action-icon-btn {
            width: 32px; height: 32px;
            padding: 0;
            border-radius: 8px;
            border: 1px solid rgba(0,107,99,0.12);
            background: #fff;
            color: var(--text-2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
            box-shadow: none;
            transition: transform .12s, background .12s, border-color .12s;
        }
        .action-icon-btn:hover { transform: translateY(-1px); text-decoration: none; }
        .action-icon-btn svg { width: 15px; height: 15px; pointer-events: none; }
        .action-icon-btn.info    { background: var(--teal-light); border-color: rgba(0,137,123,0.3); color: var(--teal-darker, var(--teal-dark)); }
        .action-icon-btn.info:hover { background: #c5ecea; }
        .action-icon-btn.warning { background: #fef3c7; border-color: #fcd34d; color: #92400e; }
        .action-icon-btn.warning:hover { background: #fde68a; }
        .action-icon-btn.danger  { background: #fee2e2; border-color: #fca5a5; color: #991b1b; }
        .action-icon-btn.danger:hover { background: #fecaca; }
        button.action-icon-btn { width: 32px; height: 32px; padding: 0; border-radius: 8px; box-shadow: none; }
        button.action-icon-btn.danger { background: #fee2e2; border-color: #fca5a5; color: #991b1b; }
        button.action-icon-btn.danger:hover { background: #fecaca; box-shadow: none; transform: translateY(-1px); }

        /* ── Misc ── */
        .actions { display: flex; flex-wrap: wrap; gap: 0.75rem; }
        .flex-spacer { flex: 1; }
        .mt-large { margin-top: 3rem; }
        .mb-0 { margin-bottom: 0; }
        .cursor-pointer { cursor: pointer; }
        .check-row { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
        .check-row span { color: var(--muted); font-size: 0.875rem; }
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            padding: 1rem;
            margin: 1rem 0 1.25rem;
            background: var(--surface-alt);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
        }
        .summary strong { color: var(--text); }

        /* ── Pagination ── */
        .pagination-wrap {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
        }
        .pagination-wrap nav[role="navigation"] {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.4rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 999px;
            box-shadow: var(--shadow-sm);
        }
        .pagination-wrap nav[role="navigation"] > div { display: flex; align-items: center; gap: 0.3rem; }
        .pagination-wrap nav[role="navigation"] a,
        .pagination-wrap nav[role="navigation"] span[aria-disabled="true"],
        .pagination-wrap nav[role="navigation"] span[aria-current="page"] {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.15rem;
            height: 2.15rem;
            padding: 0 0.65rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            transition: background .15s, color .15s, transform .15s;
        }
        .pagination-wrap nav[role="navigation"] a { color: var(--text-2); background: #fff; border: 1px solid var(--border); }
        .pagination-wrap nav[role="navigation"] a:hover { background: var(--teal-soft); border-color: var(--teal-border); transform: translateY(-1px); text-decoration: none; color: var(--teal-dark); }
        .pagination-wrap nav[role="navigation"] span[aria-disabled="true"] { color: rgba(107,150,144,0.5); background: rgba(247,253,251,0.8); border: 1px solid var(--border); cursor: not-allowed; }
        .pagination-wrap nav[role="navigation"] span[aria-current="page"] { color: #fff; background: var(--grad-teal); border: none; box-shadow: 0 4px 12px rgba(0,107,99,0.3); }
        .pagination-wrap nav[role="navigation"] svg { width: 1rem; height: 1rem; }
        .pagination-wrap nav[role="navigation"] .hidden { display: none; }

        @media (max-width: 700px) {
            .topbar { padding: 0.6rem 0.75rem; border-radius: 14px; position: static; }
            .brand-text { display: none; }
            h1 { font-size: 1.3rem; }
        }
    </style>
    @stack('head')
</head>
<body>
    <div class="container">
        <header class="topbar">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                </span>
                <span class="brand-text">
                    <strong>{{ config('app.name') }}</strong>
                    <span>Billetterie &amp; événements</span>
                </span>
            </a>

            <nav>
                <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Accueil</a>
                @guest
                    <a class="nav-cta" href="{{ route('login') }}">Connexion</a>
                    <a class="nav-cta" href="{{ route('register') }}">Inscription</a>
                @else
                    <a href="{{ route('my.tickets') }}" @class(['active' => request()->routeIs('my.tickets')])>Mes billets</a>
                    @if(auth()->user()?->is_admin || auth()->user()?->is_superadmin)
                        <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.*')])>Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;margin:0;" onsubmit="return confirm('Voulez-vous vraiment vous déconnecter ?');">
                        @csrf
                        <button type="submit" class="logout-btn">Déconnexion</button>
                    </form>
                @endguest
            </nav>
        </header>

        @if(!request()->routeIs('home'))
            <div class="page-back">
                <button type="button" class="back-btn back-btn-js" data-fallback="{{ route('home') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
                    Retour
                </button>
            </div>
        @endif

        @yield('content')
    </div>
    <script>
        document.querySelectorAll('.back-btn-js').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var fallback = btn.getAttribute('data-fallback') || '/';

                if (window.history.length > 1) {
                    window.history.back();
                    return;
                }

                window.location.href = fallback;
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
