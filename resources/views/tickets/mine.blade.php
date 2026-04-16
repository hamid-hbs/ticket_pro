@extends('layouts.app')

@section('title', 'Mes billets — '.config('app.name'))

@section('content')
<style>
    /* ── Page header ── */
    .tickets-hero { margin-bottom: 2rem; }
    .tickets-hero h1 {
        font-size: 1.8rem; font-weight: 900; letter-spacing: -.04em;
        background: var(--grad-teal); -webkit-background-clip: text;
        background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: .2rem;
    }
    .tickets-hero p { color: var(--muted); font-size: .88rem; margin: 0; }
    .tickets-count {
        display: inline-flex; align-items: center; gap: .35rem;
        margin-top: .6rem; padding: .3rem .75rem;
        border-radius: 999px; background: var(--teal-soft);
        border: 1px solid var(--teal-border);
        font-size: .75rem; font-weight: 700; color: var(--teal-dark);
    }

    /* ── Ticket list ── */
    .t-list { display: flex; flex-direction: column; gap: .9rem; }

    .ticket-item {
        border-radius: 14px;
        border: 1px solid var(--border);
        background: #fff;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .ticket-item summary {
        list-style: none;
        cursor: pointer;
        padding: .9rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .8rem;
        background: linear-gradient(180deg, #ffffff, #f8fcfb);
    }
    .ticket-item summary::-webkit-details-marker { display: none; }
    .ticket-item-title {
        font-size: .95rem;
        font-weight: 800;
        color: var(--text);
        letter-spacing: -.01em;
    }
    .ticket-item-index {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 26px;
        height: 26px;
        margin-right: .45rem;
        border-radius: 999px;
        background: var(--teal-soft);
        border: 1px solid var(--teal-border);
        color: var(--teal-dark);
        font-size: .75rem;
        font-weight: 800;
        line-height: 1;
        vertical-align: middle;
    }
    .ticket-item-meta {
        margin-top: .15rem;
        font-size: .78rem;
        color: var(--muted);
        font-weight: 600;
    }
    .ticket-item-arrow {
        width: 30px; height: 30px;
        border-radius: 999px;
        border: 1px solid var(--teal-border);
        background: var(--teal-soft);
        color: var(--teal-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: none;
        transition: transform .2s ease;
    }
    .ticket-item[open] .ticket-item-arrow { transform: rotate(90deg); }
    .ticket-item-content { padding: 0 .9rem .9rem; }

    /* ── Ticket card — horizontal layout ── */
    .ticket-card {
        border-radius: 18px;
        overflow: hidden;
        display: flex;
        background: #fff;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        position: relative;
        transition: transform .22s ease, box-shadow .22s ease;
    }
    .ticket-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }

    /* Number badge */
    .ticket-num {
        position: absolute;
        top: .75rem; left: .75rem;
        z-index: 5;
        width: 26px; height: 26px;
        border-radius: 50%;
        background: rgba(255,255,255,.25);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,.4);
        color: #fff;
        font-size: .72rem; font-weight: 800;
        display: flex; align-items: center; justify-content: center;
    }

    /* Left — teal strip with event info */
    .ticket-left {
        flex: none;
        width: 52%;
        background: var(--grad-teal);
        padding: 1.35rem 1.25rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: .5rem;
        position: relative;
    }

    /* Orange top accent stripe */
    .ticket-left::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: var(--grad-orange);
    }

    .ticket-event-name {
        font-size: 1.1rem; font-weight: 900;
        color: #fff; letter-spacing: -.02em;
        line-height: 1.25;
        margin-top: .4rem;
    }

    .ticket-event-meta {
        display: flex; flex-direction: column; gap: .3rem;
        margin-top: .1rem;
    }
    .ticket-meta-row {
        display: flex; align-items: center; gap: .45rem;
        font-size: .78rem; color: rgba(255,255,255,.85);
    }
    .ticket-meta-row svg { width: 12px; height: 12px; flex: none; color: rgba(255,255,255,.7); }

    .ticket-status-chip {
        display: inline-flex; align-items: center; gap: .28rem;
        padding: .22rem .65rem;
        border-radius: 999px;
        font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em;
        margin-top: auto;
        width: fit-content;
    }
    .ticket-status-chip svg { width: 10px; height: 10px; }
    .chip-paid { background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.35); color: #fff; }
    .chip-used { background: rgba(0,0,0,.18); border: 1px solid rgba(255,255,255,.15); color: rgba(255,255,255,.65); }

    /* Tear separator */
    .ticket-sep {
        flex: none;
        width: 0;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .ticket-sep::before {
        content: '';
        position: absolute;
        top: 0; bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 1px;
        background: repeating-linear-gradient(
            to bottom,
            var(--border) 0px,
            var(--border) 6px,
            transparent 6px,
            transparent 12px
        );
    }
    .ticket-sep .notch-top,
    .ticket-sep .notch-bottom {
        width: 20px; height: 20px;
        border-radius: 50%;
        background: var(--bg);
        border: 1px solid var(--border);
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
    }
    .ticket-sep .notch-top  { top: -10px; }
    .ticket-sep .notch-bottom { bottom: -10px; }

    /* Right — QR + details */
    .ticket-right {
        flex: 1;
        padding: 1.25rem 1.1rem 1.1rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: .65rem;
    }

    .ticket-holder {
        font-size: .72rem; font-weight: 700; color: var(--muted);
        text-transform: uppercase; letter-spacing: .07em;
    }
    .ticket-holder-name { font-size: .92rem; font-weight: 800; color: var(--text); margin-top: .05rem; }

    .ticket-ref {
        font-size: .7rem; color: var(--muted);
    }
    .ticket-ref span { font-weight: 700; color: var(--text-2); font-family: ui-monospace, 'Cascadia Code', monospace; }

    /* QR */
    .ticket-qr {
        display: flex; flex-direction: column; align-items: center; gap: .4rem;
        padding: .65rem;
        border-radius: 12px;
        background: var(--teal-light);
        border: 1px solid var(--teal-border);
        text-align: center;
        margin-top: auto;
    }
    .ticket-qr img {
        width: 80px; height: 80px;
        object-fit: contain;
        background: #fff;
        border-radius: 8px;
        padding: 5px;
        border: 1px solid var(--teal-border);
    }
    .ticket-qr-code {
        font-family: ui-monospace, 'Cascadia Code', monospace;
        font-size: .66rem; font-weight: 700;
        color: var(--teal-dark);
        word-break: break-all; letter-spacing: .02em;
    }

    /* PDF btn */
    .ticket-pdf-btn {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .42rem .85rem;
        border-radius: 999px; font-size: .75rem; font-weight: 700;
        color: #fff; background: var(--grad-orange);
        border: none; cursor: pointer; text-decoration: none;
        box-shadow: 0 4px 12px rgba(224,114,18,.3);
        transition: transform .15s, box-shadow .15s;
        align-self: flex-start;
    }
    .ticket-pdf-btn:hover { transform: translateY(-1px); box-shadow: 0 7px 18px rgba(224,114,18,.4); color: #fff; text-decoration: none; }
    .ticket-pdf-btn svg { width: 13px; height: 13px; }

    /* Warning */
    .ticket-warn {
        display: flex; gap: .4rem;
        padding: .5rem .7rem;
        border-radius: 8px;
        background: rgba(217,119,6,.08);
        border: 1px solid rgba(217,119,6,.2);
        font-size: .7rem; color: #92400e; line-height: 1.5;
    }
    .ticket-warn svg { flex: none; width: 13px; height: 13px; margin-top: 1px; }

    /* Empty */
    .tickets-empty {
        text-align: center; padding: 4rem 2rem;
        border-radius: 20px; border: 2px dashed var(--teal-border);
        background: var(--teal-soft);
    }
    .tickets-empty svg { width: 52px; height: 52px; color: var(--teal); opacity: .45; margin: 0 auto 1rem; }
    .tickets-empty h2 { font-size: 1.2rem; color: var(--teal-dark); margin-bottom: .35rem; }
    .tickets-empty p { color: var(--muted); margin-bottom: 1.25rem; }

    @media (max-width: 580px) {
        .ticket-card { flex-direction: column; }
        .ticket-left { width: 100%; }
        .ticket-sep { display: none; }
        .ticket-right { padding: 1rem; }
        .ticket-num { top: .6rem; left: .6rem; }
    }
</style>

<!-- Header -->
<div class="tickets-hero">
    <h1>Mes billets</h1>
    <p>Tous les billets associés à votre compte.</p>
    @if($tickets->isNotEmpty())
        <div class="tickets-count">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/></svg>
            {{ $tickets->count() }} billet{{ $tickets->count() > 1 ? 's' : '' }}
        </div>
    @endif
</div>

@if ($tickets->isEmpty())
    <div class="tickets-empty">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
        <h2>Aucun billet pour le moment</h2>
        <p>Achetez votre premier billet pour commencer.</p>
        <a href="{{ route('buy') }}" class="btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/></svg>
            Acheter un billet
        </a>
    </div>
@else
    <div style="margin-bottom:1.25rem;">
        <a href="{{ route('buy') }}" class="btn secondary" style="border-radius:999px;font-size:.82rem;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Acheter un autre billet
        </a>
    </div>

    <div class="t-list">
        @foreach ($tickets as $ticket)
            @php
                $eventDate = $ticket->event?->date
                    ? \Illuminate\Support\Carbon::parse($ticket->event->date)->locale('fr')->translatedFormat('d F Y')
                    : 'Date non définie';
                $eventTime = $ticket->event?->start_time
                    ? \Illuminate\Support\Carbon::parse($ticket->event->start_time)->format('H:i')
                    : null;
                $purchaseAt = $ticket->created_at
                    ? $ticket->created_at->locale('fr')->translatedFormat('D d/m/Y à H:i')
                    : 'Date d\'achat non disponible';
            @endphp

            <details class="ticket-item">
                <summary>
                    <div>
                        <div class="ticket-item-title">
                            <span class="ticket-item-index">{{ $tickets->count() - $loop->index }}</span>
                            <span>{{ $ticket->event?->title ?? 'Événement' }}</span>
                        </div>
                        <div class="ticket-item-meta">Achat: {{ $purchaseAt }}</div>
                    </div>
                    <span class="ticket-item-arrow" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><path d="M9 18l6-6-6-6"/></svg>
                    </span>
                </summary>

                <div class="ticket-item-content">
                    <div class="ticket-card">
                        <!-- Numéro -->
                        <span class="ticket-num">{{ $tickets->count() - $loop->index }}</span>

                        <!-- Gauche : infos événement -->
                        <div class="ticket-left">
                            <div class="ticket-event-name">{{ $ticket->event?->title ?? 'Événement' }}</div>

                    <div class="ticket-event-meta">
                        <span class="ticket-meta-row">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $eventDate }}
                        </span>
                        @if($eventTime)
                        <span class="ticket-meta-row">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $eventTime }}
                        </span>
                        @endif
                        @if($ticket->event?->location)
                        <span class="ticket-meta-row">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $ticket->event->location }}
                        </span>
                        @endif
                        <span class="ticket-meta-row">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            {{ $ticket->email }}
                        </span>
                    </div>

                            @if($ticket->status === 'paid')
                                <span class="ticket-status-chip chip-paid">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Payé
                                </span>
                            @elseif($ticket->status === 'used')
                                <span class="ticket-status-chip chip-used">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                    Utilisé
                                </span>
                            @endif
                        </div>

                        <!-- Séparateur en tirets -->
                        <div class="ticket-sep">
                            <div class="notch-top"></div>
                            <div class="notch-bottom"></div>
                        </div>

                        <!-- Droite : QR + titulaire -->
                        <div class="ticket-right">
                            <div>
                                <div class="ticket-holder">Titulaire</div>
                                <div class="ticket-holder-name">{{ $ticket->name }}</div>
                            </div>

                            @if($ticket->payment_reference)
                            <div class="ticket-ref">
                                Réf. <span>{{ $ticket->payment_reference }}</span>
                            </div>
                            @endif

                            <!-- QR code -->
                            <div class="ticket-qr">
                                @if(!empty($ticket->qr_data_uri))
                                    <img src="{{ $ticket->qr_data_uri }}" alt="QR billet {{ $ticket->qr_code }}">
                                @else
                                    <div style="width:80px;height:80px;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:.68rem;text-align:center;background:#fff;border-radius:8px;border:1px solid var(--teal-border);">QR<br>indisponible</div>
                                @endif
                                <div class="ticket-qr-code">{{ $ticket->qr_code }}</div>
                            </div>

                            <!-- Avertissement -->
                            <div class="ticket-warn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                <span>Ne partagez pas ce QR — toute utilisation par un tiers invalide votre accès.</span>
                            </div>

                            <!-- PDF -->
                            <a href="{{ route('my.tickets.pdf', $ticket) }}" class="ticket-pdf-btn" target="_blank" rel="noopener">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Télécharger PDF
                            </a>
                        </div>
                    </div>
                </div>
            </details>
        @endforeach
    </div>
@endif
@endsection
