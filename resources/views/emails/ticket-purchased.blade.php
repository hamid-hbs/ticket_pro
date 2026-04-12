<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Billet confirmé</title>
</head>
<body style="font-family: system-ui, sans-serif; line-height: 1.5; color: #1a1a1a; max-width: 560px; margin: 0 auto; padding: 24px;">
    <h1 style="font-size: 1.25rem;">Votre billet est prêt</h1>
    <p>Bonjour <strong>{{ $ticket->name }}</strong>,</p>
    <p>Votre billet a été émis. Voici le récapitulatif :</p>
    <ul style="padding-left: 1.25rem;">
        <li><strong>Email :</strong> {{ $ticket->email }}</li>
        @if($ticket->event)
            <li><strong>Événement :</strong> {{ $ticket->event->title }}</li>
            <li><strong>Date :</strong> {{ \Illuminate\Support\Carbon::parse($ticket->event->date)->translatedFormat('d F Y') }}</li>
            <li><strong>Lieu :</strong> {{ $ticket->event->location }}</li>
        @endif
        <li><strong>Référence :</strong> {{ $ticket->payment_reference ?? '—' }}</li>
    </ul>

    <p><strong>Votre billet : image + code texte</strong></p>
    <p style="font-size: 0.9rem; color: #444;">L’image ci-dessous est intégrée à ce message. Le même QR est aussi en pièce jointe <strong>{{ $qrAttachmentName }}</strong> et dans le PDF.</p>

    <p style="text-align: center; margin: 20px 0 12px;">
        <img src="{{ $message->embedData($qrPng, 'qrcode-inline.png', 'image/png') }}"
             alt="QR code — code billet {{ $ticket->qr_code }}"
             width="280"
             height="280"
             style="display: inline-block; border: 1px solid #e5e5e5; border-radius: 8px;">
    </p>

    <p style="font-size: 0.8125rem; color: #666; text-align: center; margin: 0 0 20px;">Scannez cette image ou ouvrez la pièce jointe PNG.</p>

    <p style="font-size: 0.875rem; font-weight: 600; margin-bottom: 8px;">Code billet (texte — pour saisie manuelle à l’entrée)</p>
    <div style="font-family: ui-monospace, Consolas, monospace; font-size: 0.8125rem; background: #f4f4f5; border: 1px solid #d4d4d8; border-radius: 8px; padding: 12px 14px; word-break: break-all; letter-spacing: 0.02em; color: #18181b; user-select: all;">
        {{ $ticket->qr_code }}
    </div>

    <p style="font-size: 0.8125rem; color: #555; margin-top: 20px;">Une copie <strong>PDF</strong> complète est jointe à cet email.</p>
</body>
</html>
