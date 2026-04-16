@extends('layouts.admin', ['title' => 'Tickets'])

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
    .sbar-group input, .sbar-group select {
        border:none; background:transparent; padding:0;
        font-size:.88rem; color:var(--text); flex:1;
    }
    .sbar-group input:focus, .sbar-group select:focus { outline:none; box-shadow:none; }
    .sbar-group input::placeholder, .sbar-group select { color:var(--muted); }
    .sbar-sep { width:1px; height:20px; background:var(--border); flex:none; }
    .sbar-btn {
        padding:.38rem .85rem; font-size:.78rem; font-weight:700;
        border-radius:999px; background:var(--grad-teal); color:#fff;
        border:none; cursor:pointer; flex:none;
        transition: opacity .15s;
    }
    .sbar-btn:hover { opacity:.88; transform:none; box-shadow:none; }

    /* ── Simple list rows ── */
    .t-list { display:flex; flex-direction:column; gap:.55rem; }

    .row-num {
        flex: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--teal-soft);
        border: 1px solid var(--teal-border);
        color: var(--teal-dark);
        font-size: .7rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
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

    /* Avatar */
    .t-av {
        width:38px; height:38px; border-radius:10px;
        background:var(--grad-teal); display:flex; align-items:center;
        justify-content:center; flex:none;
        font-weight:800; font-size:.88rem; color:#fff; letter-spacing:-.02em;
        box-shadow: 0 3px 10px rgba(0,107,99,.25);
    }

    /* Main info */
    .t-main { flex:1; min-width:0; }
    .t-name { font-weight:700; font-size:.88rem; color:var(--text); }
    .t-detail {
        display:flex; flex-wrap:wrap; align-items:center;
        gap:.2rem .65rem; font-size:.74rem; color:var(--muted); margin-top:.15rem;
    }
    .t-detail-item { display:inline-flex; align-items:center; gap:.2rem; }
    .t-detail-item svg { width:11px; height:11px; flex:none; }

    /* Right side */
    .t-right { display:flex; align-items:center; gap:.7rem; flex:none; }
    .t-date { font-size:.72rem; color:var(--muted); font-weight:500; white-space:nowrap; }

    /* Empty */
    .empty-box {
        text-align:center; padding:3rem 2rem;
        border-radius:14px; border:2px dashed var(--border); background:var(--surface); color:var(--muted);
    }
    .empty-box svg { width:40px; height:40px; margin:0 auto .75rem; opacity:.35; }
</style>

<div class="page-top">
    <h1>Tickets</h1>
    @if(auth()->user()?->isSuperAdmin())
        <a href="{{ route('admin.tickets.create') }}" class="btn" style="border-radius:999px;padding:.55rem 1.1rem;font-size:.82rem;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Ajouter
        </a>
    @endif
</div>

@if(session('status'))
    <div class="flash-ok">{{ session('status') }}</div>
@endif

<form method="GET" action="{{ route('admin.tickets') }}">
    <div class="sbar">
        <div class="sbar-group">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Nom, email, QR…" autocomplete="off">
        </div>
        <div class="sbar-sep"></div>
        <div class="sbar-group" style="flex:none;min-width:unset;">
            <select name="status" id="status" style="min-width:110px;">
                <option value="">Tous</option>
                <option value="paid"  @selected(request('status')==='paid')>Payé</option>
                <option value="used"  @selected(request('status')==='used')>Utilisé</option>
            </select>
        </div>
        <button type="submit" class="sbar-btn">Filtrer</button>
    </div>
</form>

@if($tickets->isEmpty())
    <div class="empty-box">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/></svg>
        <div style="font-weight:700;margin-bottom:.2rem;">Aucun ticket trouvé</div>
        <div style="font-size:.82rem;">Modifiez vos filtres de recherche.</div>
    </div>
@else
    <div class="t-list">
        @foreach($tickets as $t)
            @php
                $initials = collect(explode(' ',$t->name))->take(2)->map(fn($w)=>strtoupper($w[0]??''))->join('');
                $rowNumber = $tickets->total() - (($tickets->firstItem() ?? 1) + $loop->index) + 1;
            @endphp
            <div class="t-row">
                <span class="row-num">{{ $rowNumber }}</span>
                <div class="t-av">{{ $initials ?: '#' }}</div>

                <div class="t-main">
                    <div class="t-name">{{ $t->name }}</div>
                    <div class="t-detail">
                        <span class="t-detail-item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            {{ $t->email }}
                        </span>
                        @if($t->event)
                        <span class="t-detail-item">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $t->event->title }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="t-right">
                    @if($t->status==='paid')
                        <span class="badge badge-paid">Payé</span>
                    @elseif($t->status==='used')
                        <span class="badge badge-used">Utilisé</span>
                    @endif
                    <div class="t-date">{{ $t->created_at?->format('d/m/Y') }}</div>
                    <div class="action-icon-group">
                        <a href="{{ route('admin.tickets.show',$t) }}" class="action-icon-btn info" title="Voir">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                        @if(auth()->user()?->isSuperAdmin())
                        <a href="{{ route('admin.tickets.edit',$t) }}" class="action-icon-btn warning" title="Modifier">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="pagination-wrap">{{ $tickets->links() }}</div>
@endsection
