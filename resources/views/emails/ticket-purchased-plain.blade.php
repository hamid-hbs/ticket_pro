Votre billet est prêt

Bonjour {{ $ticket->name }},

Votre billet a été émis.

Récapitulatif :

@if($ticket->event)
@php
	$eventDate = $ticket->event?->date
		? \Illuminate\Support\Carbon::parse($ticket->event->date)->translatedFormat('d F Y')
		: 'Date non définie';
	$eventStartTime = $ticket->event?->start_time
		? \Illuminate\Support\Carbon::parse($ticket->event->start_time)->format('H:i')
		: 'Heure non définie';
@endphp
Événement : {{ $ticket->event->title }}
Date : {{ $eventDate }}
Heure de début : {{ $eventStartTime }}
Lieu : {{ $ticket->event->location }}

@endif
Email : {{ $ticket->email }}
Référence paiement : {{ $ticket->payment_reference ?? '—' }}

---
Code billet (identique au contenu du QR code, saisie manuelle si besoin) :
{{ $ticket->qr_code }}
---

Pièces jointes : image PNG du QR ({{ $qrAttachmentName }}), billet PDF.

Présentez le QR à l’entrée ou indiquez ce code à l’accueil.
