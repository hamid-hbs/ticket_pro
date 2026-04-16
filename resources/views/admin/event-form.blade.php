@extends('layouts.admin', ['title' => $event->exists ? 'Modifier un événement' : 'Nouvel événement'])

@section('admin_content')
    <h1>{{ $event->exists ? 'Modifier un événement' : 'Nouvel événement' }}</h1>

    @if ($errors->any())
        <div class="flash-err">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card" style="max-width: 620px;">
        <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
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

            <div class="form-group">
                <label for="posters">Affiches</label>
                <input id="posters" type="file" name="posters[]" accept="image/*" multiple>
                <p class="page-note" style="margin-top:0.5rem;">Vous pouvez ajouter plusieurs affiches pour le même événement.</p>
                @if ($event->exists && $event->posters->isNotEmpty())
                    <div class="poster-strip" style="grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); margin-top: 0.75rem;">
                        @foreach ($event->posters as $poster)
                            <a href="{{ $poster->url() }}" target="_blank" rel="noreferrer" style="display:block;">
                                <img src="{{ $poster->url() }}" alt="Affiche de {{ $event->title }}" style="width:100%;aspect-ratio:3/4;object-fit:cover;border-radius:14px;border:1px solid rgba(15,23,42,0.08);">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <button type="submit">{{ $buttonLabel }}</button>
        </form>
    </div>
@endsection