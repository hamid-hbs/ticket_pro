@extends('layouts.app')

@section('title', 'Créer un compte — '.config('app.name'))

@section('content')
<style>
    .auth-wrap {
        max-width: 420px;
        margin: 3rem auto 0;
    }
    .auth-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 2rem 1.75rem;
        box-shadow: var(--shadow-md);
    }
    .auth-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        background: var(--grad-teal);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.25rem;
        box-shadow: 0 6px 18px rgba(0,107,99,.3);
    }
    .auth-icon svg { width: 26px; height: 26px; color: #fff; }
    .auth-card h1 { text-align: center; font-size: 1.45rem; margin-bottom: .3rem; }
    .auth-card .auth-sub { text-align: center; color: var(--muted); font-size: .88rem; margin-bottom: 1.5rem; }
    .auth-card .btn { width: 100%; justify-content: center; margin-top: .25rem; }
    .auth-footer {
        margin-top: 1.25rem;
        text-align: center;
        font-size: .84rem;
        color: var(--muted);
    }
    .auth-footer a { color: var(--teal-dark); font-weight: 700; }
    .auth-footer a:hover { text-decoration: underline; }
</style>

<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
        </div>

        <h1>Créer un compte</h1>
        <p class="auth-sub">Inscription gratuite pour accéder à votre espace.</p>

        @if ($errors->any())
            <div class="flash-err">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn">S'inscrire</button>
        </form>

        <div class="auth-footer">
            Déjà inscrit ?
            <a href="{{ route('login') }}">Se connecter</a>
        </div>
    </div>
</div>
@endsection