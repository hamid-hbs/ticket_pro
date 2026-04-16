@extends('layouts.admin', ['title' => $ticket->exists ? 'Modifier un ticket' : 'Nouveau ticket'])

@section('admin_content')
    <h1>{{ $ticket->exists ? 'Modifier un ticket' : 'Nouveau ticket' }}</h1>

    @if ($errors->any())
        <div class="flash-err">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card" style="max-width: 620px;">
        <form method="POST" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="form-group">
                <label for="name">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name', $ticket->name) }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $ticket->email) }}" required>
            </div>

            <div class="form-group">
                <label for="event_id">Événement</label>
                <select id="event_id" name="event_id" required>
                    <option value="">Choisir un événement</option>
                    @foreach ($events as $event)
                        @php
                            $eventDate = $event->date
                                ? (\Illuminate\Support\Carbon::parse($event->date)->translatedFormat('d F Y'))
                                : 'Date non définie';
                            $eventStartTime = $event->start_time
                                ? (\Illuminate\Support\Carbon::parse($event->start_time)->format('H:i'))
                                : 'Heure non définie';
                        @endphp
                        <option value="{{ $event->id }}" @selected((string) old('event_id', $ticket->event_id) === (string) $event->id)>
                            {{ $event->title }} — {{ $eventDate }} à {{ $eventStartTime }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status">Statut</label>
                <select id="status" name="status" required>
                    <option value="paid" @selected(old('status', $ticket->status) === 'paid')>Payé</option>
                    <option value="used" @selected(old('status', $ticket->status) === 'used')>Utilisé</option>
                </select>
            </div>

            <div class="form-group">
                <label for="qr_code">QR code</label>
                <input id="qr_code" type="text" name="qr_code" value="{{ old('qr_code', $ticket->qr_code) }}" required>
            </div>

            <div class="form-group">
                <label for="payment_reference">Référence paiement</label>
                <input id="payment_reference" type="text" name="payment_reference" value="{{ old('payment_reference', $ticket->payment_reference) }}" placeholder="Optionnel">
            </div>

            <button type="submit">{{ $buttonLabel }}</button>
        </form>
    </div>
@endsection
