<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        td { padding: 6px 0; border-bottom: 1px solid #eee; }
        td:first-child { width: 35%; font-weight: bold; color: #444; }
        .qr { text-align: center; margin-top: 24px; }
    </style>
</head>
<body>
    <h1>Billet d’accès</h1>
    @if($ticket->event)
        @php
            $eventDate = $ticket->event?->date
                ? \Illuminate\Support\Carbon::parse($ticket->event->date)->format('d/m/Y')
                : 'Date non définie';
            $eventStartTime = $ticket->event?->start_time
                ? \Illuminate\Support\Carbon::parse($ticket->event->start_time)->format('H:i')
                : 'Heure non définie';
        @endphp
        <p><strong>{{ $ticket->event->title }}</strong></p>
        <p>{{ $eventDate }} à {{ $eventStartTime }} — {{ $ticket->event->location }}</p>
    @endif
    <table>
        <tr><td>Nom</td><td>{{ $ticket->name }}</td></tr>
        <tr><td>Email</td><td>{{ $ticket->email }}</td></tr>
        <tr><td>Statut</td><td>Payé</td></tr>
        <tr><td>Référence paiement</td><td>{{ $ticket->payment_reference ?? '—' }}</td></tr>
        <tr><td>Code billet</td><td style="font-family: monospace; font-size: 10px;">{{ $ticket->qr_code }}</td></tr>
    </table>
    <div class="qr">
        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(180)->generate($ticket->qr_code) !!}
    </div>
</body>
</html>
