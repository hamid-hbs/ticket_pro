@extends('layouts.admin', ['title' => 'Ticket #'.$ticket->id])

@section('admin_content')
    <style>
        .ticket-show-wrap {
            display: grid;
            gap: 1rem;
        }
        .ticket-show-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .ticket-show-head h1 {
            margin: 0;
        }
        .ticket-code {
            font-family: ui-monospace, "Cascadia Code", monospace;
            font-size: 0.75rem;
            word-break: break-all;
            padding: 0.35rem 0.55rem;
            border-radius: 8px;
            background: rgba(0, 137, 123, 0.1);
            border: 1px solid rgba(0, 137, 123, 0.24);
            color: #0f4d43;
        }
        .ticket-show-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 1rem;
        }
        .ticket-meta-list {
            display: grid;
            gap: 0.75rem;
        }
        .ticket-meta-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding-bottom: 0.6rem;
            border-bottom: 1px solid var(--border);
        }
        .ticket-meta-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .ticket-meta-item span:first-child {
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .ticket-meta-item span:last-child {
            color: var(--text);
            font-weight: 700;
            text-align: right;
        }
        .ticket-status {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.65rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .ticket-status.paid {
            background: rgba(16, 185, 129, 0.14);
            border: 1px solid rgba(16, 185, 129, 0.28);
            color: #065f46;
        }
        .ticket-status.used {
            background: rgba(100, 116, 139, 0.16);
            border: 1px solid rgba(100, 116, 139, 0.28);
            color: #334155;
        }
        .ticket-status.unpaid {
            background: rgba(245, 158, 11, 0.14);
            border: 1px solid rgba(245, 158, 11, 0.28);
            color: #92400e;
        }
        .ticket-side-card {
            display: grid;
            gap: 0.7rem;
        }
        .ticket-side-title {
            margin: 0;
            font-size: 0.86rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .ticket-side-value {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
            color: var(--text);
        }
        @media (max-width: 860px) {
            .ticket-show-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
        $statusLabel = $ticket->status === 'paid' ? 'Payé' : ($ticket->status === 'used' ? 'Utilisé' : 'Non payé');
        $statusClass = $ticket->status === 'paid' ? 'paid' : ($ticket->status === 'used' ? 'used' : 'unpaid');
    @endphp

    <div class="ticket-show-wrap">
        <div class="ticket-show-head">
            <h1>Ticket #{{ $ticket->id }}</h1>
            <span class="ticket-code">{{ $ticket->qr_code }}</span>
        </div>

        <div class="ticket-show-grid">
            <div class="card">
                <div class="ticket-meta-list">
                    <div class="ticket-meta-item"><span>Nom</span><span>{{ $ticket->name }}</span></div>
                    <div class="ticket-meta-item"><span>Email</span><span>{{ $ticket->email }}</span></div>
                    <div class="ticket-meta-item"><span>Événement</span><span>{{ $ticket->event?->title ?? '—' }}</span></div>
                    <div class="ticket-meta-item"><span>Réf. paiement</span><span>{{ $ticket->payment_reference ?? '—' }}</span></div>
                    <div class="ticket-meta-item"><span>Utilisé le</span><span>{{ $ticket->used_at?->format('d/m/Y H:i:s') ?? '—' }}</span></div>
                    @if($ticket->status === 'used')
                        <div class="ticket-meta-item"><span>Scanné par</span><span>{{ $ticket->scannedBy?->name ?? '—' }}</span></div>
                    @endif
                    <div class="ticket-meta-item"><span>Email envoyé</span><span>{{ $ticket->email_sent_at?->format('d/m/Y H:i:s') ?? '—' }}</span></div>
                </div>
            </div>

            <div class="card ticket-side-card">
                <p class="ticket-side-title">Statut</p>
                <span class="ticket-status {{ $statusClass }}">{{ $statusLabel }}</span>

                <p class="ticket-side-title">Code QR (secret)</p>
                <p class="ticket-side-value ticket-code" style="margin:0;">{{ $ticket->qr_code }}</p>

                @if($ticket->event?->date)
                    <p class="ticket-side-title">Date événement</p>
                    <p class="ticket-side-value">{{ $ticket->event->date->format('d/m/Y') }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="actions" style="margin-top:1rem;">
        @if(auth()->user()?->isSuperAdmin())
            <a class="btn warning" href="{{ route('admin.tickets.edit', $ticket) }}">Modifier</a>
            <form method="POST" action="{{ route('admin.tickets.destroy', $ticket) }}" onsubmit="return confirm('Supprimer ce ticket ?');" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger">Supprimer</button>
            </form>
        @endif
    </div>
@endsection
