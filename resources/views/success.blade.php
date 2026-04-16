@extends('layouts.app')

@section('title', 'Paiement réussi')

@section('content')
    <div class="card card--narrow">
        <h1>Paiement réussi</h1>
        <div class="flash-ok">Merci <strong>{{ $ticket->name }}</strong>.</div>
        <p class="page-note">Un email avec votre billet (QR code et PDF) a été envoyé à <strong>{{ $ticket->email }}</strong> si le paiement est confirmé.</p>
        @if($ticket->event)
            @php
                $eventDate = $ticket->event->date
                    ? \Illuminate\Support\Carbon::parse($ticket->event->date)->translatedFormat('d F Y')
                    : 'Date non définie';
                $eventStartTime = $ticket->event->start_time
                    ? \Illuminate\Support\Carbon::parse($ticket->event->start_time)->format('H:i')
                    : 'Heure non définie';
            @endphp
            <p>{{ $ticket->event->title }} — {{ $eventDate }} à {{ $eventStartTime }}</p>
        @endif
    </div>
@endsection
