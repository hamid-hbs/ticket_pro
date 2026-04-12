@extends('layouts.admin', ['title' => 'Tickets'])

@section('admin_content')
    <h1>Tickets</h1>

    @if (session('status'))
        <div class="flash-ok">{{ session('status') }}</div>
    @endif

    @if(auth()->user()?->isSuperAdmin())
        <p style="margin-bottom:1rem;"><a href="{{ route('admin.tickets.create') }}">+ Ajouter un ticket</a></p>
    @endif

    <form method="GET" action="{{ route('admin.tickets') }}" class="card" style="margin-bottom:1rem;">
        <div class="actions" style="align-items:flex-end;">
            <div class="form-group" style="min-width: 220px; flex: 1; margin-bottom: 0;">
                <label for="q">Recherche</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Nom, email, QR…">
            </div>
            <div class="form-group" style="min-width: 180px; margin-bottom: 0;">
                <label for="status">Statut</label>
                <select id="status" name="status">
                    <option value="">Tous</option>
                    <option value="paid" @selected(request('status')==='paid')>Payé</option>
                    <option value="used" @selected(request('status')==='used')>Utilisé</option>
                </select>
            </div>
            <button type="submit">Filtrer</button>
        </div>
    </form>

    <div class="card" style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Événement</th>
                    <th>Vendu par</th>
                    <th>Statut</th>
                    <th>Créé</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $t)
                    <tr>
                        <td>{{ $t->id }}</td>
                        <td>{{ $t->name }}</td>
                        <td>{{ $t->email }}</td>
                        <td>{{ $t->event?->title ?? '—' }}</td>
                        <td>{{ $t->soldBy?->name ?? '—' }}</td>
                        <td>
                            @if ($t->status === 'paid')
                                <span class="badge badge-paid">Payé</span>
                            @elseif ($t->status === 'used')
                                <span class="badge badge-used">Utilisé</span>
                            @endif
                        </td>
                        <td>{{ $t->created_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="action-icon-group">
                                <a href="{{ route('admin.tickets.show', $t) }}" class="action-icon-btn info" title="Détail" aria-label="Voir le détail">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                @if(auth()->user()?->isSuperAdmin())
                                    <a href="{{ route('admin.tickets.edit', $t) }}" class="action-icon-btn warning" title="Modifier" aria-label="Modifier le ticket">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">Aucun ticket.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">{{ $tickets->links() }}</div>
@endsection
