@extends('layouts.app')

@section('title', 'Connexion admin')

@section('content')
    <div class="card" style="max-width: 420px; margin: 3rem auto;">
        <h1>Administration</h1>
        <p style="color: var(--muted); font-size: 0.9rem;">Connexion réservée aux comptes administrateur.</p>

        @if ($errors->any())
            <div class="flash-err">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
            <div style="margin-bottom: 1rem;">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>
            <label style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem; cursor:pointer;">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                <span style="color:var(--muted); font-size:0.875rem;">Se souvenir de moi</span>
            </label>
            <button type="submit">Se connecter</button>
        </form>
    </div>
@endsection
