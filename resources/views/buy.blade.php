@extends('layouts.app')

@section('title', 'Acheter un billet')

@section('content')
    <div class="card" style="max-width:480px;">
        <h1>Acheter un billet</h1>
        <p style="color:var(--muted); font-size:0.9rem;">{{ $event->title }} — {{ \Illuminate\Support\Carbon::parse($event->date)->translatedFormat('d F Y') }}, {{ $event->location }} ({{ number_format($event->price, 0, ',', ' ') }} FCFA)</p>

        @if ($errors->any())
            <div class="flash-err">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/buy">
            @csrf
            <div style="margin-bottom:1rem;">
                <label for="name">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <input type="hidden" name="event_id" value="{{ $event->id }}">
            <button type="submit">Continuer vers le paiement</button>
        </form>
        <p style="margin-top:1.5rem; font-size:0.85rem; color:var(--muted);">
            <a href="{{ route('login') }}">Espace administrateur</a>
        </p>
    </div>
@endsection
