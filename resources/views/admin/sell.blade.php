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
        $selectedEventId = old('event_id', $events->first()->id);
    @endphp

    <div class="card card--narrow">
        <form method="POST" action="{{ route('admin.sell.store') }}">
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
                        <option
                            value="{{ $event->id }}"
                            data-title="{{ $event->title }}"
                            data-date="{{ $eventDate }}"
                            data-time="{{ $eventStartTime }}"
                            data-location="{{ $event->location }}"
                            data-price="{{ number_format($event->price, 0, ',', ' ') }}"
                            @selected((string) $selectedEventId === (string) $event->id)
                        >
                            {{ $event->title }} — {{ $eventDate }} à {{ $eventStartTime }} — {{ $event->location }}
                        </option>
                    @endforeach
                </select>
            </div>

            @php
                $selectedEvent = $events->firstWhere('id', (int) $selectedEventId) ?? $events->first();
                $selectedEventDate = $selectedEvent->date
                    ? (\Illuminate\Support\Carbon::parse($selectedEvent->date)->translatedFormat('d F Y'))
                    : 'Date non définie';
                $selectedEventStartTime = $selectedEvent->start_time
                    ? (\Illuminate\Support\Carbon::parse($selectedEvent->start_time)->format('H:i'))
                    : 'Heure non définie';
            @endphp

            <p class="page-note">Événement: <strong id="selected-event-title">{{ $selectedEvent->title }}</strong><br><span id="selected-event-date">{{ $selectedEventDate }}</span> à <span id="selected-event-time">{{ $selectedEventStartTime }}</span>, <span id="selected-event-location">{{ $selectedEvent->location }}</span> (<span id="selected-event-price">{{ number_format($selectedEvent->price, 0, ',', ' ') }}</span> FCFA)</p>

            <div class="form-group">
                <label for="name">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="email">Email <span style="font-size:0.85rem; color:#666;">(doit être réel, ex: Gmail)</span></label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <button type="submit">Vendre et envoyer le billet</button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const eventSelect = document.getElementById('event_id');
    const titleEl = document.getElementById('selected-event-title');
    const dateEl = document.getElementById('selected-event-date');
    const timeEl = document.getElementById('selected-event-time');
    const locationEl = document.getElementById('selected-event-location');
    const priceEl = document.getElementById('selected-event-price');

    if (!eventSelect || !titleEl || !dateEl || !timeEl || !locationEl || !priceEl) {
        return;
    }

    const updatePreview = () => {
        const selectedOption = eventSelect.options[eventSelect.selectedIndex];
        if (!selectedOption) {
            return;
        }

        titleEl.textContent = selectedOption.dataset.title || 'Événement';
        dateEl.textContent = selectedOption.dataset.date || 'Date non définie';
        timeEl.textContent = selectedOption.dataset.time || 'Heure non définie';
        locationEl.textContent = selectedOption.dataset.location || 'Lieu non défini';
        priceEl.textContent = selectedOption.dataset.price || '0';
    };

    eventSelect.addEventListener('change', updatePreview);
    updatePreview();
});
</script>
@endpush