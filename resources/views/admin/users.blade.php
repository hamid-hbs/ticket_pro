@extends('layouts.admin', ['title' => 'Utilisateurs'])

@section('admin_content')
<style>
    .page-top {
        display:flex; align-items:center; justify-content:space-between;
        flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem;
    }
    .page-top h1 { margin:0; }

    /* Search bar */
    .sbar {
        display:flex; align-items:center; gap:.6rem;
        background:var(--surface); border:1px solid var(--border);
        border-radius:12px; padding:.6rem .85rem;
        box-shadow:var(--shadow-sm); margin-bottom:1.5rem; flex-wrap:wrap;
    }
    .sbar-group { display:flex; align-items:center; gap:.5rem; flex:1; min-width:160px; }
    .sbar-group svg { color:var(--muted); width:15px; height:15px; flex:none; }
    .sbar-group input {
        border:none; background:transparent; padding:0;
        font-size:.88rem; color:var(--text); flex:1;
    }
    .sbar-group input:focus { outline:none; box-shadow:none; }
    .sbar-group input::placeholder { color:var(--muted); }
    .sbar-btn {
        padding:.38rem .85rem; font-size:.78rem; font-weight:700;
        border-radius:999px; background:var(--grad-teal); color:#fff;
        border:none; cursor:pointer; flex:none;
        transition: opacity .15s;
    }
    .sbar-btn:hover { opacity:.88; transform:none; box-shadow:none; }

    /* User list */
    .t-list { display:flex; flex-direction:column; gap:.55rem; }

    .row-num {
        flex: none;
        width: 24px; height: 24px;
        border-radius: 50%;
        background: var(--teal-soft);
        border: 1px solid var(--teal-border);
        color: var(--teal-dark);
        font-size: .7rem; font-weight: 800;
        display: flex; align-items: center; justify-content: center;
    }

    .t-row {
        display:flex; align-items:center; gap:.9rem;
        padding:.85rem 1rem;
        background:var(--surface);
        border:1px solid var(--border);
        border-radius:12px;
        box-shadow:var(--shadow-sm);
        transition: border-color .18s, box-shadow .18s;
    }
    .t-row:hover { border-color:var(--teal-border); box-shadow:var(--shadow-md); }

    .t-av {
        width:38px; height:38px; border-radius:10px;
        background:var(--grad-teal); display:flex; align-items:center;
        justify-content:center; flex:none;
        font-weight:800; font-size:.88rem; color:#fff; letter-spacing:-.02em;
        box-shadow: 0 3px 10px rgba(0,107,99,.25);
    }

    .t-main { flex:1; min-width:0; }
    .t-name { font-weight:700; font-size:.88rem; color:var(--text); }
    .t-detail {
        display:flex; flex-wrap:wrap; align-items:center;
        gap:.2rem .65rem; font-size:.74rem; color:var(--muted); margin-top:.15rem;
    }
    .t-detail-item { display:inline-flex; align-items:center; gap:.2rem; }
    .t-detail-item svg { width:11px; height:11px; flex:none; }

    .t-right { display:flex; align-items:center; gap:.7rem; flex:none; }
    .t-date { font-size:.72rem; color:var(--muted); font-weight:500; white-space:nowrap; }

    /* Empty */
    .empty-card {
        text-align: center;
        padding: 3.5rem 2rem;
        border-radius: 18px;
        border: 2px dashed var(--border);
        background: var(--surface);
        color: var(--muted);
    }
    .empty-card svg { width: 44px; height: 44px; margin-bottom: 0.75rem; opacity: 0.4; }
</style>

<div class="page-top">
    <h1>Utilisateurs</h1>
    <a href="{{ route('admin.users.create') }}" class="btn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouvel utilisateur
    </a>
</div>

@if (session('status'))
    <div class="flash-ok">{{ session('status') }}</div>
@endif

<!-- Search -->
<form method="GET" action="{{ route('admin.users') }}">
    <div class="sbar">
        <div class="sbar-group">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Rechercher par nom ou email…" autocomplete="off">
        </div>
        <button type="submit" class="sbar-btn">Filtrer</button>
    </div>
</form>

<!-- User list -->
@if ($users->isEmpty())
    <div class="empty-card">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <div style="font-weight:700;margin-bottom:0.25rem;">Aucun utilisateur trouvé</div>
        <div style="font-size:0.85rem;">Modifiez vos critères de recherche.</div>
    </div>
@else
    <div class="t-list">
        @foreach ($users as $user)
            @php
                $initials = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->join('');
                $rowNumber = $users->total() - (($users->firstItem() ?? 1) + $loop->index) + 1;
            @endphp
            <div class="t-row">
                <span class="row-num">{{ $rowNumber }}</span>
                <div class="t-av">{{ $initials ?: '?' }}</div>

                <div class="t-main">
                    <div class="t-name">{{ $user->name }}</div>
                    <div class="t-detail">
                        <span class="t-detail-item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            {{ $user->email }}
                        </span>
                    </div>
                </div>

                <div class="t-right">
                    <div style="text-align:right;">
                        @if ($user->isSuperAdmin())
                            <span class="badge badge-superadmin">Superadmin</span>
                        @elseif ($user->isAdmin())
                            <span class="badge badge-admin">Admin</span>
                        @else
                            <span class="badge badge-user">Utilisateur</span>
                        @endif
                        <div class="t-date">{{ $user->created_at?->format('d/m/Y') }}</div>
                    </div>

                    <div class="action-icon-group">
                        <a href="{{ route('admin.users.edit', $user) }}" class="action-icon-btn warning" title="Modifier">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                        </a>
                        @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-icon-btn danger" title="Supprimer">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="pagination-wrap">{{ $users->links() }}</div>
@endsection
