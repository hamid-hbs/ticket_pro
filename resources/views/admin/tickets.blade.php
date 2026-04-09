@extends('layouts.admin', ['title' => 'Tickets'])

@section('admin_content')
    <h1>Tickets</h1>

    @if (session('status'))
        <div class="flash-ok">{{ session('status') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.tickets') }}" class="card" style="margin-bottom:1rem; padding:1rem;">
        <div style="display:flex; flex-wrap:wrap; gap:1rem; align-items:flex-end;">
            <div>
                <label for="q">Recherche</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Nom, email, QR…">
            </div>
            <div>
                <label for="status">Statut</label>
                <select id="status" name="status" style="padding:0.6rem; border-radius:8px; border:1px solid #334155; background:#0f172a; color:inherit;">
                    <option value="">Tous</option>
                    <option value="pending" @selected(request('status')==='pending')>En attente</option>
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
                        <td>
                            @if ($t->status === 'paid')
                                <span class="badge badge-paid">Payé</span>
                            @elseif ($t->status === 'used')
                                <span class="badge badge-used">Utilisé</span>
                            @else
                                <span class="badge badge-pending">En attente</span>
                            @endif
                        </td>
                        <td>{{ $t->created_at?->format('d/m/Y H:i') }}</td>
                        <td><a href="{{ route('admin.tickets.show', $t) }}">Détail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7">Aucun ticket.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem;">{{ $tickets->links() }}</div>
@endsection
