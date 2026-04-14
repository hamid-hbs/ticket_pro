<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Votre billet est prêt</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: #f6f8ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2937;
        }

        .container {
            max-width: 420px;
            margin: 0 auto;
        }

        .header {
            background: #059F83;
            color: #ffffff;
            padding: 20px;
            border-radius: 14px;
        }

        .header small {
            font-size: 12px;
            opacity: 0.8;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 8px 0;
            font-size: 20px;
            line-height: 1.25;
        }

        .header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .card {
            background: #ffffff;
            border-radius: 14px;
            padding: 18px;
            margin-top: 15px;
            border: 1px solid #cfeee8;
        }

        .section-title {
            font-size: 13px;
            font-weight: 600;
            margin: 0 0 12px;
            color: #059F83;
        }

        .row {
            width: 100%;
            margin-bottom: 10px;
            display: table;
            table-layout: fixed;
        }

        .label {
            color: #475569;
            font-size: 13px;
            display: table-cell;
            width: 38%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        .value {
            display: table-cell;
            font-size: 13px;
            font-weight: 600;
            color: #0b254f;
            width: 62%;
            text-align: right;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        .qr-block {
            margin-top: 15px;
            padding: 15px;
            border-radius: 12px;
            background: #f8fbff;
            border: 1px solid #cfeee8;
            text-align: center;
        }

        .qr-image {
            display: inline-block;
            width: 120px;
            height: 120px;
            padding: 10px;
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid #059F83;
        }

        .qr-help {
            font-size: 11px;
            color: #475569;
            margin: 8px 0 0;
        }

        .warning {
            margin-top: 12px;
            padding: 10px;
            border-radius: 10px;
            background: #fff3e8;
            border: 1px solid #E07B1A;
            color: #B85B00;
            font-size: 11px;
            line-height: 1.5;
            text-align: left;
        }

        .code {
            margin-top: 15px;
            padding: 12px;
            background: #059F83;
            color: #ffffff;
            border-radius: 10px;
            font-size: 12px;
            text-align: center;
            letter-spacing: 1px;
            word-break: break-all;
        }

        .footer {
            font-size: 11px;
            color: #475569;
            margin-top: 10px;
        }

        @media only screen and (max-width: 520px) {
            body {
                padding: 12px !important;
            }

            .container {
                max-width: 100% !important;
            }

            .header,
            .card {
                padding: 16px !important;
            }

            .row {
                margin-bottom: 8px !important;
            }

            .label,
            .value {
                font-size: 12px !important;
                line-height: 1.2 !important;
            }

            .label {
                width: 36% !important;
            }

            .value {
                width: 64% !important;
            }

            .code {
                font-size: 11px !important;
                letter-spacing: 0.6px !important;
            }
        }
    </style>
</head>
<body>
    @php
        $eventDate = $ticket->event?->date
            ? \Illuminate\Support\Carbon::parse($ticket->event->date)->locale('fr')->translatedFormat('d F Y')
            : 'Date non définie';
        $eventStartTime = $ticket->event?->start_time
            ? \Illuminate\Support\Carbon::parse($ticket->event->start_time)->format('H:i')
            : 'Heure non définie';
    @endphp
    <div class="container">
        <div class="header">
            <small>Ticket Pro</small>
            <h2>Votre billet est prêt</h2>
            <p>Bonjour <strong>{{ $ticket->name }}</strong>, votre billet a été émis avec succès.</p>
        </div>

        <div class="card">
            <div class="section-title">Récapitulatif du billet</div>

            <div class="row">
                <span class="label">Email</span>
                <span class="value">{{ $ticket->email }}</span>
            </div>

            @if($ticket->event)
                <div class="row">
                    <span class="label">Événement</span>
                    <span class="value">{{ $ticket->event->title }}</span>
                </div>

                <div class="row">
                    <span class="label">Date</span>
                    <span class="value">{{ $eventDate }}</span>
                </div>

                <div class="row">
                    <span class="label">Heure de début</span>
                    <span class="value">{{ $eventStartTime }}</span>
                </div>

                <div class="row">
                    <span class="label">Lieu</span>
                    <span class="value">{{ $ticket->event->location }}</span>
                </div>
            @endif

            <div class="row">
                <span class="label">Référence</span>
                <span class="value">{{ $ticket->payment_reference ?? '—' }}</span>
            </div>

            <div class="qr-block">
                <img src="{{ $message->embedData($qrPng, 'qrcode-inline.png', 'image/png') }}"
                     alt="QR code — code billet {{ $ticket->qr_code }}"
                     class="qr-image">
                 <p class="qr-help">Scannez ce code à l'entrée de l'événement.</p>

                <div class="warning">
                    <strong>Avertissement :</strong> ne partagez pas ce QR code avec une autre personne.
                    Toute utilisation du même code par un tiers peut invalider votre accès à l'événement.
                </div>
            </div>

            <div class="code">
                {{ $ticket->qr_code }}
            </div>

            <div class="footer">
                Une copie PDF complète est jointe à cet email.
            </div>
        </div>
    </div>
</body>
</html>
