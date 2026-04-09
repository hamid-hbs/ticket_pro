@extends('layouts.app')

@section('title', 'Acheter un billet')

@section('content')
    <div class="card card--narrow">
        <h1>Acheter un billet</h1>
        <p class="page-note">{{ $event->title }} — {{ \Illuminate\Support\Carbon::parse($event->date)->translatedFormat('d F Y') }}, {{ $event->location }} ({{ number_format($event->price, 0, ',', ' ') }} FCFA)</p>

        @if ($errors->any())
            <div class="flash-err">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/buy">
            @csrf
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <input type="hidden" name="event_id" value="{{ $event->id }}">
            <button type="submit">Continuer vers le paiement</button>
        </form>
        <!--<p class="page-note mt-large mb-0">
            <a href="{{ route('login') }}">Espace administrateur</a>
        </p>-->
    </div>
@endsection
