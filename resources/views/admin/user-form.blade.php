@extends('layouts.admin', ['title' => $user->exists ? 'Modifier un utilisateur' : 'Nouvel utilisateur'])

@section('admin_content')
    <h1>{{ $user->exists ? 'Modifier un utilisateur' : 'Nouvel utilisateur' }}</h1>

    @if ($errors->any())
        <div class="flash-err">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card" style="max-width: 520px;">
        <form method="POST" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="form-group">
                <label for="name">Nom</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" {{ $user->exists ? '' : 'required' }} placeholder="{{ $user->exists ? 'Laisser vide pour conserver le mot de passe' : '' }}">
            </div>

            <label class="check-row cursor-pointer">
                <input type="checkbox" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                <span>Admin</span>
            </label>

            <label class="check-row cursor-pointer">
                <input type="checkbox" name="is_superadmin" value="1" {{ old('is_superadmin', $user->is_superadmin) ? 'checked' : '' }}>
                <span>Superadmin</span>
            </label>

            <p class="page-note">Le superadmin hérite automatiquement des privilèges admin.</p>

            <button type="submit">{{ $buttonLabel }}</button>
        </form>
    </div>
@endsection
