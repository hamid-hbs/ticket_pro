@extends('layouts.app')

@section('title', config('app.name').' - Accueil')

@section('content')
<style>
    /* ── Hero ── */
    .home-hero {
        text-align: center;
        padding: 2rem 1rem 2.25rem;
        margin-bottom: 2.25rem;
    }
    .home-hero h1 {
        margin: 0 0 0.5rem;
        font-size: clamp(2rem, 5vw, 3rem);
        font-weight: 900;
        letter-spacing: -0.04em;
        background: var(--grad-hero);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        line-height: 1.15;
    }
    .home-hero p { margin: 0 auto; max-width: 460px; color: var(--muted); font-size: 1rem; }
    .hero-flourish {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        margin: 0 0 .9rem;
        padding: .35rem .8rem;
        border-radius: 999px;
        background: rgba(255,255,255,.8);
        border: 1px solid rgba(0,137,123,.12);
        box-shadow: 0 8px 24px rgba(0,81,74,.06);
    }
    .hero-flourish span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--grad-teal);
        box-shadow: 0 0 0 4px rgba(0,137,123,.08);
    }
    .hero-flourish i {
        width: 42px;
        height: 2px;
        border-radius: 999px;
        background: linear-gradient(90deg, transparent, rgba(0,137,123,.55), transparent);
        display: block;
    }
    .hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 1rem;
        padding: 0.38rem 0.9rem;
        border-radius: 999px;
        background: var(--teal-soft);
        border: 1px solid var(--teal-border);
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--teal-dark);
    }
    .hero-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: var(--grad-teal);
        animation: pdot 2s infinite;
    }
    @keyframes pdot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.6;transform:scale(1.35)} }

    /* ── Feed ── */
    .feed-wrap {
        max-width: 560px;
        margin: 0 auto;
    }

    .feed-head {
        margin-bottom: 1.25rem;
        padding: 1rem 1.1rem;
        border-radius: 18px;
        background: rgba(255,255,255,.86);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
    }
    .feed-head h2 {
        margin: 0;
        font-size: 1.05rem;
        color: var(--text);
        font-weight: 800;
        letter-spacing: -.02em;
    }
    .feed-head p {
        margin: .3rem 0 0;
        color: var(--muted);
        font-size: .9rem;
    }

    /* ── Poster card ── */
    .poster-feed {
        display: flex;
        flex-direction: column;
        gap: .9rem;
    }
    .poster-card {
        appearance: none;
        -webkit-appearance: none;
        text-align: left;
        padding: 0;
        cursor: pointer;
        border-radius: 24px;
        overflow: hidden;
        background: #fff;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        transition: transform .28s cubic-bezier(.22,.68,0,1.2), box-shadow .28s ease;
        margin: 0 auto;
        width: 100%;
        max-width: 360px;
    }
    .poster-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 56px rgba(0,81,74,0.14);
    }
    .poster-card:focus-visible {
        outline: 3px solid rgba(0, 137, 123, 0.28);
        outline-offset: 3px;
    }

    /* Image */
    .poster-image {
        width: 100%;
        aspect-ratio: 4 / 4.4;
        background: linear-gradient(145deg, var(--teal-light), #d4f5f2);
        overflow: hidden;
        flex: none;
        position: relative;
    }
    .poster-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .poster-image .poster-placeholder {
        padding: 1rem;
    }
    .poster-meta {
        position: absolute;
        left: .85rem;
        top: .85rem;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .28rem .62rem;
        border-radius: 999px;
        background: rgba(0,0,0,.42);
        color: #fff;
        font-size: .72rem;
        font-weight: 700;
        backdrop-filter: blur(6px);
    }
    .poster-meta svg {
        width: 11px;
        height: 11px;
        flex: none;
    }

    .poster-caption {
        padding: .6rem .85rem .7rem;
        display: grid;
        gap: .25rem;
        border-top: 1px solid rgba(15,23,42,.06);
    }
    .poster-caption h3 {
        margin: 0;
        font-size: .94rem;
        color: var(--text);
        font-weight: 800;
        letter-spacing: -.02em;
    }
    .poster-caption .caption {
        margin: 0;
        color: var(--muted);
        font-size: .8rem;
        line-height: 1.5;
    }
    .poster-caption .poster-badges {
        margin-top: .12rem;
    }

    .poster-hint {
        margin-top: .05rem;
        font-size: .72rem;
        color: var(--muted);
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }
    .poster-hint svg {
        width: 12px;
        height: 12px;
        flex: none;
    }

    /* Placeholder */
    .poster-placeholder {
        width:100%; height:100%;
        display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.65rem;
    }
    .poster-placeholder svg { width:48px; height:48px; color: var(--teal); opacity:.4; }
    .poster-placeholder span { font-size:.82rem; font-weight:600; color:var(--muted); }

    /* Footer */
    .poster-body {
        padding: 1rem 1.1rem 1.15rem;
        display: grid;
        gap: .35rem;
        border-top: 1px solid rgba(15,23,42,.06);
    }
    .poster-body h3 {
        margin: 0;
        font-size: 1rem;
        color: var(--text);
        font-weight: 800;
        letter-spacing: -.02em;
    }
    .poster-body .caption {
        margin: 0;
        color: var(--muted);
        font-size: .88rem;
        line-height: 1.5;
    }
    .poster-badges {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem;
        margin-top: .25rem;
    }
    .poster-badge {
        display: inline-flex;
        align-items: center;
        padding: .24rem .55rem;
        border-radius: 999px;
        background: var(--teal-soft);
        border: 1px solid var(--teal-border);
        color: var(--teal-dark);
        font-size: .7rem;
        font-weight: 700;
    }

    /* Empty state */
    .empty-state {
        grid-column:1/-1; text-align:center; padding:4rem 2rem;
        border-radius:22px; border:2px dashed var(--teal-border); background: var(--teal-soft);
    }
    .empty-state svg { width:52px; height:52px; color:var(--teal); opacity:.5; margin:0 auto 1rem; }
    .empty-state h2 { font-size:1.25rem; color:var(--teal-dark); margin-bottom:.35rem; }
    .empty-state p { color:var(--muted); }

    .post-modal {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.68);
        backdrop-filter: blur(10px);
    }
    .post-modal.is-open {
        display: flex;
    }
    .post-modal__panel {
        width: min(92vw, 920px);
        max-height: 92vh;
        background: transparent;
        border-radius: 0;
        overflow: visible;
        box-shadow: none;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .post-modal__media {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .post-modal__media img {
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 92vh;
        object-fit: contain;
        display: block;
    }
    .post-modal__close {
        position: absolute;
        top: .75rem;
        right: .75rem;
        width: 36px;
        height: 36px;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,.24);
        background: rgba(15,23,42,.72);
        color: #fff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }
    .post-modal__close svg { width: 16px; height: 16px; }
    .post-modal__title,
    .post-modal__meta,
    .post-modal__badges,
    .post-modal__note {
        display: none;
    }

    @media (max-width:600px) {
        .feed-wrap { max-width: 100%; }
        .poster-card { border-radius: 18px; max-width: 320px; }
        .poster-image { aspect-ratio: 4 / 4.35; }
        .poster-caption { padding: .55rem .75rem .65rem; }
    }
</style>

<!-- Hero -->
<div class="home-hero">
    <h1>IG_PARTY_9.0's</h1>
    <div class="hero-flourish" aria-hidden="true">
        <span></span>
        <i></i>
        <span></span>
    </div>
    <p>Découvrez les prochains événements et achetez vos billets en quelques secondes.</p>
    <!--<div class="hero-pill">
        <span class="hero-dot"></span>
        {{ $events->count() }} événement{{ $events->count() > 1 ? 's' : '' }} disponible{{ $events->count() > 1 ? 's' : '' }}
    </div>-->
</div>

<!-- Feed -->
<div class="feed-wrap">
    <!--<div class="feed-head">
        <h2>Fil des annonces</h2>
        <p>Chaque affiche est isolée dans son espace, comme un post Instagram.</p>
    </div>-->

    <div class="poster-feed">
        @forelse ($events as $event)
            @php
                $eventDate  = $event->date?->translatedFormat('d F Y') ?? 'Date à venir';
                $eventTime  = $event->start_time ? \Illuminate\Support\Carbon::parse($event->start_time)->format('H:i') : null;
                $posterItems = $event->posters;
                if ($posterItems->isEmpty() && $event->posterUrl()) {
                    $posterItems = collect([(object)['legacy_url' => $event->posterUrl()]]);
                }
                $totalPosters = $posterItems->count();
            @endphp

            @forelse ($posterItems as $poster)
                <button
                    type="button"
                    class="poster-card"
                    data-title="{{ $event->title }}"
                    data-date="{{ $eventDate }}"
                    data-time="{{ $eventTime ?? 'Heure à venir' }}"
                    data-location="{{ $event->location ?: 'Lieu à venir' }}"
                    data-caption="{{ $eventDate }}{{ $eventTime ? ' · '.$eventTime : '' }}{{ $event->location ? ' · '.$event->location : '' }}"
                    data-image="{{ method_exists($poster,'url') ? $poster->url() : $poster->legacy_url }}"
                    aria-label="Ouvrir l’affiche de {{ $event->title }}"
                >
                    <div class="poster-image">
                        @if(method_exists($poster,'url'))
                            <img src="{{ $poster->url() }}" alt="Affiche de {{ $event->title }}">
                        @else
                            <img src="{{ $poster->legacy_url }}" alt="Affiche de {{ $event->title }}">
                        @endif

                        <!--<div class="poster-meta">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>
                            Annonce
                        </div>-->
                    </div>

                    <div class="poster-caption">
                        <h3>{{ $event->title }}</h3>
                        <p class="caption">{{ $eventDate }}{{ $eventTime ? ' · '.$eventTime : '' }}{{ $event->location ? ' · '.$event->location : '' }}</p>
                        <div class="poster-badges">
                            <span class="poster-badge">#Affiche</span>
                            <span class="poster-badge">#{{ $event->title ?: '-' }}</span>
                        </div>
                        <div class="poster-hint">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
                            Cliquez pour voir en grand
                        </div>
                    </div>
                </button>
            @empty
                <div class="poster-card" tabindex="0">
                    <div class="poster-image">
                        <div class="poster-placeholder">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <span>Aucune affiche</span>
                        </div>
                    </div>

                    <div class="poster-caption">
                        <h3>{{ $event->title }}</h3>
                        <p class="caption">{{ $eventDate }}{{ $eventTime ? ' · '.$eventTime : '' }}{{ $event->location ? ' · '.$event->location : '' }}</p>
                        <div class="poster-badges">
                            <span class="poster-badge">#Affiche</span>
                            <span class="poster-badge">{{ $event->location ?: 'Lieu à venir' }}</span>
                        </div>
                    </div>
                </div>
            @endforelse
        @empty
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/></svg>
                <h2>Aucun événement publié</h2>
                <p>Les affiches apparaîtront ici dès qu'un événement sera créé.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="post-modal" id="post-modal" aria-hidden="true">
    <div class="post-modal__panel" role="dialog" aria-modal="true" aria-labelledby="post-modal-title">
        <div class="post-modal__media">
            <img id="post-modal-image" src="" alt="Affiche en grand">
            <button type="button" class="post-modal__close" id="post-modal-close" aria-label="Fermer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('post-modal');
    var modalImage = document.getElementById('post-modal-image');
    var modalClose = document.getElementById('post-modal-close');

    function openModal(card) {
        modalImage.src = card.dataset.image;
        modalImage.alt = 'Affiche de ' + card.dataset.title;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.poster-card[data-image]').forEach(function (card) {
        card.addEventListener('click', function () {
            openModal(card);
        });
        card.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openModal(card);
            }
        });
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    modalClose.addEventListener('click', closeModal);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
})();
</script>
@endsection