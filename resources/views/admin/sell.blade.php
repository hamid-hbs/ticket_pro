@extends('layouts.admin', ['title' => 'Vendre un billet'])

@section('admin_content')
    <h1>Vendre un billet</h1>
    <p class="page-note">Enregistrez le nom et l’email du client, puis le billet lui sera envoyé automatiquement.</p>

    @if (session('status'))
        <div class="flash-ok">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="flash-err">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @php
        $eventDate = $event->date
            ? (\Illuminate\Support\Carbon::parse($event->date)->translatedFormat('d F Y'))
            : 'Date non définie';
    @endphp

    <div class="card card--narrow">
        <p class="page-note">Événement: <strong>{{ $event->title }}</strong><br>{{ $eventDate }}, {{ $event->location }} ({{ number_format($event->price, 0, ',', ' ') }} FCFA)</p>

        <form method="POST" action="{{ route('admin.sell.store') }}">
            @csrf
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="email">Email <span style="font-size:0.85rem; color:#666;">(doit être réel, ex: Gmail)</span></label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <input type="hidden" name="event_id" value="{{ $event->id }}">
            <button type="submit">Vendre et envoyer le billet</button>
        </form>
    </div>
@endsection