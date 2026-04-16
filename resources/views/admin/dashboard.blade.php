@extends('layouts.admin', ['title' => 'Tableau de bord'])

@section('admin_content')
<style>
    .dash-title {
        font-size: 1.75rem;
        font-weight: 900;
        letter-spacing: -.04em;
        color: var(--teal-deeper, var(--teal-dark));
        margin-bottom: .2rem;
    }
    .dash-sub { color: var(--muted); font-size: .88rem; margin-bottom: 2rem; }

    /* ─ Stats ─ */
    .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px,1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.2rem 1rem;
        text-align: center;
        box-shadow: var(--shadow-sm);
        transition: transform .2s, box-shadow .2s;
    }
    .stat:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto .75rem;
        background: var(--grad-teal);
        box-shadow: 0 5px 14px rgba(0,107,99,.3);
    }
    .stat-icon svg { width: 20px; height: 20px; color: #fff; }
    .stat strong {
        display: block; font-size: 1.9rem; font-weight: 900; line-height: 1.05;
        margin-bottom: .2rem;
        background: var(--grad-teal);
        -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
    }
    .stat span { font-size: .7rem; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: .07em; }

    /* ─ Action cards ─ */
    .section-label { font-size: .72rem; font-weight: 800; color: var(--muted); text-transform: uppercase; letter-spacing: .09em; margin-bottom: .85rem; }
    .dash-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap: .85rem; }
    .dash-link {
        display: flex; align-items: center; gap: .9rem;
        padding: 1rem 1.1rem;
        border-radius: 14px;
        text-decoration: none !important;
        background: var(--surface);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        transition: transform .2s, box-shadow .2s, border-color .2s;
        color: var(--text);
    }
    .dash-link:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--teal-border); text-decoration: none !important; }
    .dash-link-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex: none;
    }
    .dash-link-icon svg { width: 18px; height: 18px; }
    .teal-icon  { background: var(--teal-soft); color: var(--teal-dark); }
    .orange-icon { background: var(--orange-soft); color: var(--orange-dark); }
    .choco-icon  { background: rgba(44,21,7,.08); color: var(--choco-soft, #4A2B15); }
    .dash-link-text strong { display: block; font-size: .88rem; font-weight: 700; color: var(--text); }
    .dash-link-text span   { font-size: .74rem; color: var(--muted); }
    .dash-link-arrow { margin-left: auto; color: var(--muted); flex: none; }
    .dash-link-arrow svg { width: 15px; height: 15px; }
</style>

<div class="dash-title">Tableau de bord</div>
<p class="dash-sub">Bonjour <strong>{{ auth()->user()->name }}</strong> — voici votre activité.</p>

<!-- Stats -->
<div class="stats">
    <div class="stat">
        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/></svg></div>
        <strong>{{ $total }}</strong><span>Total billets</span>
    </div>
    <div class="stat">
        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
        <strong>{{ $paid }}</strong><span>Payés</span>
    </div>
    <div class="stat">
        <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
        <strong>{{ $used }}</strong><span>Utilisés</span>
    </div>
    @if(auth()->user()?->isSuperAdmin())
        <div class="stat">
            <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
            <strong>{{ \App\Models\User::count() }}</strong><span>Utilisateurs</span>
        </div>
        <div class="stat">
            <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <strong>{{ \App\Models\Event::count() }}</strong><span>Événements</span>
        </div>
    @endif
</div>

<!-- Quick links -->
<p class="section-label">Actions rapides</p>
<div class="dash-grid">
    <a href="{{ route('admin.tickets') }}" class="dash-link">
        <div class="dash-link-icon teal-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/></svg></div>
        <div class="dash-link-text"><strong>Tickets</strong><span>Voir &amp; gérer les billets</span></div>
        <div class="dash-link-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></div>
    </a>

    <a href="{{ route('admin.scan') }}" class="dash-link">
        <div class="dash-link-icon orange-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
        <div class="dash-link-text"><strong>Scanner</strong><span>Valider l'entrée par QR</span></div>
        <div class="dash-link-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></div>
    </a>

    @if(auth()->user()?->isSuperAdmin())
        <a href="{{ route('admin.events') }}" class="dash-link">
            <div class="dash-link-icon teal-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div class="dash-link-text"><strong>Événements</strong><span>Créer et gérer les événements</span></div>
            <div class="dash-link-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></div>
        </a>

        <a href="{{ route('admin.users') }}" class="dash-link">
            <div class="dash-link-icon choco-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div class="dash-link-text"><strong>Utilisateurs</strong><span>Rôles et comptes</span></div>
            <div class="dash-link-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></div>
        </a>
    @endif
</div>
@endsection
