@extends('layouts.admin', ['title' => 'Ticket #'.$ticket->id])

@section('admin_content')
    <p><a href="{{ route('admin.tickets') }}">← Retour à la liste</a></p>
    <h1>Ticket #{{ $ticket->id }}</h1>
    <div class="card">
        <table>
            <tr><td>Nom</td><td>{{ $ticket->name }}</td></tr>
            <tr><td>Email</td><td>{{ $ticket->email }}</td></tr>
            <tr><td>Événement</td><td>{{ $ticket->event?->title ?? '—' }}</td></tr>
            <tr><td>Vendu par</td><td>{{ $ticket->soldBy?->name ?? '—' }}</td></tr>
            <tr>
                <td>Statut</td>
                <td>
                    @if($ticket->status === 'paid')
                        Payé
                    @elseif($ticket->status === 'used')
                        Utilisé
                    @else
                        Non payé
                    @endif
                </td>
            </tr>
            <tr><td>QR (secret)</td><td style="font-family:monospace; font-size:0.75rem; word-break:break-all;">{{ $ticket->qr_code }}</td></tr>
            <tr><td>Réf. paiement</td><td>{{ $ticket->payment_reference ?? '—' }}</td></tr>
            <tr><td>Utilisé le</td><td>{{ $ticket->used_at?->format('d/m/Y H:i:s') ?? '—' }}</td></tr>
            @if($ticket->status === 'used')
                <tr>
                    <td>Scanné par</td>
                    <td>{{ $ticket->scannedBy?->name ?? '—' }}</td>
                </tr>
            @endif
            <tr><td>Email envoyé</td><td>{{ $ticket->email_sent_at?->format('d/m/Y H:i:s') ?? '—' }}</td></tr>
        </table>
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
