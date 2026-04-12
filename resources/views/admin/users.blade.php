@extends('layouts.admin', ['title' => 'Utilisateurs'])

@section('admin_content')
    <h1>Utilisateurs</h1>

    @if (session('status'))
        <div class="flash-ok">{{ session('status') }}</div>
    @endif

    <p style="margin-bottom:1rem;"><a href="{{ route('admin.users.create') }}">+ Nouvel utilisateur</a></p>

    <form method="GET" action="{{ route('admin.users') }}" class="card" style="margin-bottom:1rem;">
        <div class="actions" style="align-items:flex-end;">
            <div class="form-group" style="min-width: 220px; flex: 1; margin-bottom: 0;">
                <label for="q">Recherche</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Nom ou email">
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
                    <th>Rôle</th>
                    <th>Créé</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->isSuperAdmin())
                                <span class="badge badge-superadmin">Superadmin</span>
                            @elseif ($user->isAdmin())
                                <span class="badge badge-admin">Admin</span>
                            @else
                                <span class="badge badge-user">Utilisateur</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at?->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user) }}">Modifier</a>
                            @if(auth()->id() !== $user->id)
                                ·
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="danger" style="padding:0.55rem 0.9rem; font-size:0.85rem;">Supprimer</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Aucun utilisateur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">{{ $users->links() }}</div>
@endsection
