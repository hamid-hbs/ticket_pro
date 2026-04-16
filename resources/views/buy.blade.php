@extends('layouts.app')

@section('title', 'Acheter un billet')

@section('content')
    @php
        $selectedEventId = old('event_id', $events->first()->id);
    @endphp

    <div class="card card--narrow">
        <h1>Acheter un billet</h1>
        <p class="page-note">Choisissez l’événement et complétez vos informations. Le billet est envoyé par email après paiement.</p>

        <form method="POST" action="{{ route('buy.store') }}">
            @csrf
            <div class="form-group">
                <label for="event_id">Événement</label>
                <select id="event_id" name="event_id" required>
                    @foreach ($events as $event)
                        @php
                            $eventDate = $event->date
                                ? (\Illuminate\Support\Carbon::parse($event->date)->translatedFormat('d F Y'))
                                : 'Date non définie';
                            $eventStartTime = $event->start_time
                                ? (\Illuminate\Support\Carbon::parse($event->start_time)->format('H:i'))
                                : 'Heure non définie';
                        @endphp
                        <option value="{{ $event->id }}" @selected((string) $selectedEventId === (string) $event->id)>
                            {{ $event->title }} — {{ $eventDate }} à {{ $eventStartTime }} — {{ $event->location }}
                        </option>
                    @endforeach
                </select>
            </div>

            @php
                $selectedEvent = $events->firstWhere('id', (int) $selectedEventId) ?? $events->first();
                $eventDate = $selectedEvent->date
                    ? (\Illuminate\Support\Carbon::parse($selectedEvent->date)->translatedFormat('d F Y'))
                    : 'Date non définie';
                $eventStartTime = $selectedEvent->start_time
                    ? (\Illuminate\Support\Carbon::parse($selectedEvent->start_time)->format('H:i'))
                    : 'Heure non définie';
            @endphp

            <p class="page-note">{{ $selectedEvent->title }} — {{ $eventDate }} à {{ $eventStartTime }}, {{ $selectedEvent->location }} ({{ number_format($selectedEvent->price, 0, ',', ' ') }} FCFA)</p>

        @if ($errors->any())
            <div class="flash-err">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required>
            </div>
            <div class="form-group">
                <label for="email">Email <span style="font-size:0.85rem; color:#666;">(doit être réel, ex: Gmail)</span></label>
                <input id="email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required>
            </div>
            <button type="submit">Continuer vers le paiement</button>
        </form>
    </div>
@endsection
