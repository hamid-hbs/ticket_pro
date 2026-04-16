@extends('layouts.admin', ['title' => 'Événements'])

@section('admin_content')
<style>
    .page-top {
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;
    }
    .page-top h1 { margin: 0; }

    /* Search */
    .sbar {
        display: flex; align-items: center; gap: .6rem;
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 12px; padding: .6rem .85rem;
        box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;
    }
    .sbar svg { color: var(--muted); width: 16px; height: 16px; flex: none; }
    .sbar input {
        border: none; background: transparent; padding: 0;
        font-size: .88rem; color: var(--text); flex: 1;
    }
    .sbar input:focus { outline: none; box-shadow: none; }
    .sbar input::placeholder { color: var(--muted); }
    .sbar-btn {
        padding: .38rem .85rem; font-size: .78rem; font-weight: 700;
        border-radius: 999px; background: var(--grad-teal); color: #fff;
        border: none; cursor: pointer; box-shadow: none;
        transition: opacity .15s;
    }
    .sbar-btn:hover { opacity: .88; transform: none; box-shadow: none; }

    /* ── Event cards ── */
    .ev-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px,1fr));
        gap: 1rem;
    }

    .ev-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        display: flex; flex-direction: column;
        transition: transform .2s, box-shadow .2s;
    }
    .ev-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }

    /* Thumbnail */
    .ev-thumb {
        aspect-ratio: 16/7;
        background: linear-gradient(135deg, var(--teal-light), #c8f0ec);
        overflow: hidden; flex: none; position: relative;
    }
    .ev-thumb img { width:100%; height:100%; object-fit:cover; }
    .ev-thumb-placeholder {
        width:100%; height:100%;
        display:flex; align-items:center; justify-content:center;
        color: var(--teal); opacity:.4;
    }
    .ev-thumb-placeholder svg { width:36px; height:36px; }

    /* Body */
    .ev-body { padding: .85rem 1rem; flex: 1; }
    .ev-name { font-size: .95rem; font-weight: 800; color: var(--text); letter-spacing: -.015em; margin-bottom: .35rem; }
    .ev-meta { display:flex; flex-wrap:wrap; gap:.3rem .65rem; font-size:.75rem; color:var(--muted); margin-bottom:.55rem; }
    .ev-meta-item { display:inline-flex; align-items:center; gap:.22rem; }
    .ev-meta-item svg { width:11px; height:11px; }

    .ev-chips { display:flex; flex-wrap:wrap; gap:.35rem; }
    .chip {
        display:inline-flex; align-items:center; gap:.22rem;
        padding:.18rem .55rem; border-radius:999px;
        font-size:.7rem; font-weight:700;
    }
    .chip-price { background:var(--teal-soft); color:var(--teal-dark); border:1px solid var(--teal-border); }
    .chip-photos { background:var(--orange-soft); color:var(--orange-dark); border:1px solid var(--orange-border); }

    /* Footer */
    .ev-footer {
        display:flex; align-items:center; justify-content:flex-end; gap:.4rem;
        padding:.65rem 1rem; border-top:1px solid var(--border);
    }

    /* Row number badge */
    .ev-num {
        position: absolute;
        top: .65rem; left: .65rem;
        z-index: 4;
        width: 26px; height: 26px;
        border-radius: 50%;
        background: rgba(0,0,0,.45);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,.3);
        color: #fff;
        font-size: .7rem; font-weight: 800;
        display: flex; align-items: center; justify-content: center;
    }

    /* Empty */
    .empty-box {
        grid-column:1/-1; text-align:center; padding:3rem 2rem;
        border-radius:16px; border:2px dashed var(--border); background:var(--surface);
        color:var(--muted);
    }
    .empty-box svg { width:40px; height:40px; margin:0 auto .75rem; opacity:.35; }
</style>

<div class="page-top">
    <h1>Événements</h1>
    <a href="{{ route('admin.events.create') }}" class="btn" style="border-radius:999px;padding:.55rem 1.1rem;font-size:.82rem;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouvel événement
    </a>
</div>

@if (session('status'))
    <div class="flash-ok">{{ session('status') }}</div>
@endif

<form method="GET" action="{{ route('admin.events') }}">
    <div class="sbar">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Titre ou lieu…" autocomplete="off">
        <button type="submit" class="sbar-btn">Filtrer</button>
    </div>
</form>

<div class="ev-grid">
    @forelse ($events as $event)
        @php
            $eventDate = $event->date ? \Illuminate\Support\Carbon::parse($event->date)->translatedFormat('d/m/Y') : '—';
            $eventTime = $event->start_time ? \Illuminate\Support\Carbon::parse($event->start_time)->format('H:i') : '—';
            $firstPoster = $event->posters->first();
            $posterUrl = $firstPoster
                ? (method_exists($firstPoster,'url') ? $firstPoster->url() : $firstPoster->legacy_url)
                : ($event->posterUrl() ?: null);
        @endphp
        <div class="ev-card">
            <div class="ev-thumb">
                <span class="ev-num">{{ $loop->iteration }}</span>
                @if($posterUrl)
                    <img src="{{ $posterUrl }}" alt="">
                @else
                    <div class="ev-thumb-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                @endif
            </div>

            <div class="ev-body">
                <div class="ev-name">{{ $event->title }}</div>
                <div class="ev-meta">
                    <span class="ev-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $eventDate }}
                    </span>
                    <span class="ev-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ $eventTime }}
                    </span>
                    @if($event->location)
                    <span class="ev-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $event->location }}
                    </span>
                    @endif
                </div>
                <div class="ev-chips">
                    <span class="chip chip-price">{{ number_format($event->price,0,',',' ') }} FCFA</span>
                    <span class="chip chip-photos">{{ $event->posters_count }} affiche(s)</span>
                </div>
            </div>

            <div class="ev-footer">
                <a href="{{ route('admin.events.edit', $event) }}" class="action-icon-btn warning" title="Modifier">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                </a>
                <form method="POST" action="{{ route('admin.events.destroy', $event) }}" style="display:inline;" onsubmit="return confirm('Supprimer cet événement ?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-icon-btn danger" title="Supprimer">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="empty-box">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <div style="font-weight:700;margin-bottom:.2rem;">Aucun événement</div>
            <div style="font-size:.82rem;">Créez votre premier événement.</div>
        </div>
    @endforelse
</div>

<div class="pagination-wrap">{{ $events->links() }}</div>
@endsection