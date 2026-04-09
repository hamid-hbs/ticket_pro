@extends('layouts.admin', ['title' => 'Ticket #'.$ticket->id])

@section('admin_content')
    <p><a href="{{ route('admin.tickets') }}">← Retour à la liste</a></p>
    <h1>Ticket #{{ $ticket->id }}</h1>
    <div class="card">
        <table>
            <tr><td>Nom</td><td>{{ $ticket->name }}</td></tr>
            <tr><td>Email</td><td>{{ $ticket->email }}</td></tr>
            <tr><td>Événement</td><td>{{ $ticket->event?->title ?? '—' }}</td></tr>
            <tr><td>Statut</td><td>{{ $ticket->status }}</td></tr>
            <tr><td>QR (secret)</td><td style="font-family:monospace; font-size:0.75rem; word-break:break-all;">{{ $ticket->qr_code }}</td></tr>
            <tr><td>Réf. paiement</td><td>{{ $ticket->payment_reference ?? '—' }}</td></tr>
            <tr><td>Utilisé le</td><td>{{ $ticket->used_at?->format('d/m/Y H:i:s') ?? '—' }}</td></tr>
            <tr><td>Email envoyé</td><td>{{ $ticket->email_sent_at?->format('d/m/Y H:i:s') ?? '—' }}</td></tr>
        </table>
    </div>

    <form method="POST" action="{{ route('admin.tickets.destroy', $ticket) }}" onsubmit="return confirm('Supprimer ce ticket ?');" style="margin-top:1rem;">
        @csrf
        @method('DELETE')
        <button type="submit" class="secondary" style="background:#7f1d1d; color:#fecaca;">Supprimer</button>
    </form>
@endsection
