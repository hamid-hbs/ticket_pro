Merci pour votre achat

Bonjour {{ $ticket->name }},

Votre paiement est confirmé.

@if($ticket->event)
Événement : {{ $ticket->event->title }}
Date : {{ \Illuminate\Support\Carbon::parse($ticket->event->date)->translatedFormat('d F Y') }}
Lieu : {{ $ticket->event->location }}

@endif
Email : {{ $ticket->email }}
Référence paiement : {{ $ticket->payment_reference ?? '—' }}

———
CODE BILLET (identique au contenu du QR code, saisie manuelle si besoin) :
{{ $ticket->qr_code }}
———

Pièces jointes : image PNG du QR ({{ $qrAttachmentName }}), billet PDF.

Présentez le QR à l’entrée ou indiquez ce code à l’accueil.
