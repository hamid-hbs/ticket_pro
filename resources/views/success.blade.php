@extends('layouts.app')

@section('title', 'Paiement réussi')

@section('content')
    <div class="card" style="max-width:480px;">
        <h1>Paiement réussi</h1>
        <p>Merci <strong>{{ $ticket->name }}</strong>.</p>
        <p style="color:var(--muted); font-size:0.9rem;">Un email avec votre billet (QR code et PDF) a été envoyé à <strong>{{ $ticket->email }}</strong> si le paiement est confirmé.</p>
        @if($ticket->event)
            <p>{{ $ticket->event->title }} — {{ \Illuminate\Support\Carbon::parse($ticket->event->date)->translatedFormat('d F Y') }}</p>
        @endif
        <p style="margin-top:1.5rem;"><a href="/">Retour à l’accueil</a></p>
    </div>
@endsection
