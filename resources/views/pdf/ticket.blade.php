<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #0f172a;
            background: #f2fbf8;
        }
        .ticket {
            width: 100%;
            min-height: 100vh;
            border: 1px solid #cde9e1;
            border-radius: 0;
            overflow: hidden;
            background: #ffffff;
        }
        .ticket-head {
            padding: 14px 16px;
            background: #059f83;
            color: #ffffff;
        }
        .ticket-head small {
            display: block;
            opacity: 0.85;
            letter-spacing: 0.08em;
            font-size: 10px;
            text-transform: uppercase;
        }
        .ticket-head h1 {
            margin: 6px 0 2px;
            font-size: 20px;
            line-height: 1.2;
        }
        .ticket-head p {
            margin: 0;
            font-size: 12px;
            opacity: 0.95;
        }
        .ticket-body {
            padding: 14px 16px 12px;
        }
        .event-title {
            margin: 0;
            font-size: 16px;
            color: #0f4d43;
        }
        .event-sub {
            margin: 4px 0 0;
            font-size: 12px;
            color: #475569;
        }
        .line {
            border-top: 1px dashed #cde9e1;
            margin: 12px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 6px 0;
            border-bottom: 1px solid #e8f3ef;
            vertical-align: top;
        }
        td:first-child {
            width: 36%;
            font-weight: 700;
            color: #475569;
        }
        td:last-child {
            font-weight: 700;
            color: #0f172a;
            text-align: right;
            word-break: break-word;
        }
        .qr-wrap {
            margin-top: 14px;
            text-align: center;
            background: #f2fbf8;
            border: 1px solid #cde9e1;
            border-radius: 12px;
            padding: 12px;
        }
        .qr-wrap img {
            width: 150px;
            height: 150px;
            padding: 8px;
            border-radius: 10px;
            border: 1px solid #9fd7ca;
            background: #ffffff;
        }
        .qr-help {
            margin: 8px 0 0;
            font-size: 11px;
            color: #475569;
        }
        .code {
            margin-top: 10px;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            background: #059f83;
            color: #ffffff;
            font-size: 11px;
            letter-spacing: 0.07em;
            word-break: break-all;
        }
        .foot {
            margin-top: 10px;
            font-size: 10px;
            color: #64748b;
            text-align: center;
        }
        .warn {
            margin-top: 10px;
            padding: 8px 10px;
            border-radius: 10px;
            background: #fff3e8;
            border: 1px solid #e07b1a;
            color: #b85b00;
            font-size: 10px;
            line-height: 1.45;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $eventDate = $ticket->event?->date
            ? \Illuminate\Support\Carbon::parse($ticket->event->date)->format('d/m/Y')
            : 'Date non définie';
        $eventStartTime = $ticket->event?->start_time
            ? \Illuminate\Support\Carbon::parse($ticket->event->start_time)->format('H:i')
            : 'Heure non définie';
    @endphp

    <div class="ticket">
        <div class="ticket-head">
            <small>Ticket Pro</small>
            <h1>Billet d’accès</h1>
            <p>Présentez ce billet à l’entrée de l’événement.</p>
        </div>

        <div class="ticket-body">
            @if($ticket->event)
                <p class="event-title">{{ $ticket->event->title }}</p>
                <p class="event-sub">{{ $eventDate }} à {{ $eventStartTime }} · {{ $ticket->event->location }}</p>
                <div class="line"></div>
            @endif

            <table>
                <tr><td>Nom</td><td>{{ $ticket->name }}</td></tr>
                <tr><td>Email</td><td>{{ $ticket->email }}</td></tr>
                <tr><td>Statut</td><td>{{ strtoupper($ticket->status ?? 'paid') }}</td></tr>
                <tr><td>Référence paiement</td><td>{{ $ticket->payment_reference ?? '—' }}</td></tr>
                <tr><td>Code billet</td><td style="font-family: DejaVu Sans Mono, monospace; font-size: 10px;">{{ $ticket->qr_code }}</td></tr>
            </table>

            <div class="qr-wrap">
                @if(!empty($qrPngBase64))
                    <img src="data:image/png;base64,{{ $qrPngBase64 }}" alt="QR code du billet">
                @else
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(150)->generate($ticket->qr_code) !!}
                @endif
                <p class="qr-help">Scannez ce QR code à l'entrée.</p>
            </div>

            <div class="code">{{ $ticket->qr_code }}</div>
            <div class="warn"><strong>Avertissement :</strong> ne partagez pas ce QR code. Toute utilisation par une autre personne peut invalider votre accès à l’événement.</div>
            <p class="foot">Billet personnel · ne pas partager le QR code.</p>
        </div>
    </div>
</body>
</html>
