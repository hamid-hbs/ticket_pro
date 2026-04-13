@extends('layouts.admin', ['title' => $event->exists ? 'Modifier un événement' : 'Nouvel événement'])

@section('admin_content')
    <p><a href="{{ route('admin.events') }}">← Retour à la liste</a></p>
    <h1>{{ $event->exists ? 'Modifier un événement' : 'Nouvel événement' }}</h1>

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
                <label for="title">Titre</label>
                <input id="title" type="text" name="title" value="{{ old('title', $event->title) }}" required>
            </div>

            <div class="form-group">
                <label for="price">Prix</label>
                <input id="price" type="text" name="price" value="{{ old('price', $event->price) }}" required inputmode="numeric">
            </div>

            <div class="form-group">
                <label for="date">Date</label>
                <input id="date" type="date" name="date" value="{{ old('date', optional($event->date)->format('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label for="start_time">Heure de début</label>
                <input id="start_time" type="time" name="start_time" value="{{ old('start_time', $event->start_time ? \Illuminate\Support\Carbon::parse($event->start_time)->format('H:i') : '') }}" required>
            </div>

            <div class="form-group">
                <label for="location">Lieu</label>
                <input id="location" type="text" name="location" value="{{ old('location', $event->location) }}" required>
            </div>

            <button type="submit">{{ $buttonLabel }}</button>
        </form>
    </div>
@endsection