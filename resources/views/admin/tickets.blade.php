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
                            <a href="{{ route('admin.tickets.show', $t) }}">Détail</a>
                            @if(auth()->user()?->isSuperAdmin())
                                · <a href="{{ route('admin.tickets.edit', $t) }}">Modifier</a>
                            @endif
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
