@extends('layouts.app')

@section('title', 'Paiement réussi')

@section('content')
    <div class="card card--narrow">
        <h1>Paiement réussi</h1>
        <div class="flash-ok">Merci <strong>{{ $ticket->name }}</strong>.</div>
        <p class="page-note">Un email avec votre billet (QR code et PDF) a été envoyé à <strong>{{ $ticket->email }}</strong> si le paiement est confirmé.</p>
        @if($ticket->event)
            <p>{{ $ticket->event->title }} — {{ \Illuminate\Support\Carbon::parse($ticket->event->date)->translatedFormat('d F Y') }}</p>
        @endif
        <p style="margin-top:1.25rem; margin-bottom:0;"><a href="/">Retour à l’accueil</a></p>
    </div>
@endsection
