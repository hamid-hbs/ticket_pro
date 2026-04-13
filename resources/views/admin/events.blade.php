@extends('layouts.admin', ['title' => 'Événements'])

@section('admin_content')
    <h1>Événements</h1>

    @if (session('status'))
        <div class="flash-ok">{{ session('status') }}</div>
    @endif

    <p style="margin-bottom:1rem;">
        <a href="{{ route('admin.events.create') }}">+ Nouvel événement</a>
    </p>

    <form method="GET" action="{{ route('admin.events') }}" class="card" style="margin-bottom:1rem;">
        <div class="actions" style="align-items:flex-end;">
            <div class="form-group" style="min-width: 220px; flex: 1; margin-bottom: 0;">
                <label for="q">Recherche</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Titre ou lieu">
            </div>
            <button type="submit">Filtrer</button>
        </div>
    </form>

    <div class="card" style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Titre</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Lieu</th>
                    <th>Prix</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    @php
                        $eventDate = $event->date
                            ? \Illuminate\Support\Carbon::parse($event->date)->translatedFormat('d/m/Y')
                            : '—';
                        $eventStartTime = $event->start_time
                            ? \Illuminate\Support\Carbon::parse($event->start_time)->format('H:i')
                            : '—';
                    @endphp
                    <tr>
                        <td>{{ $event->id }}</td>
                        <td>{{ $event->title }}</td>
                        <td>{{ $eventDate }}</td>
                        <td>{{ $eventStartTime }}</td>
                        <td>{{ $event->location }}</td>
                        <td>{{ number_format($event->price, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <div class="action-icon-group">
                                <a href="{{ route('admin.events.edit', $event) }}" class="action-icon-btn warning" title="Modifier" aria-label="Modifier l'événement">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}" style="display:inline;" onsubmit="return confirm('Supprimer cet événement et tous ses billets associés ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-icon-btn danger" title="Supprimer" aria-label="Supprimer l'événement">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">Aucun événement.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">{{ $events->links() }}</div>
@endsection