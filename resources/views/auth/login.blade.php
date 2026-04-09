@extends('layouts.app')

@section('title', 'Connexion admin')

@section('content')
    <div class="card card--narrow mt-large">
        <h1>Administration</h1>
        <p class="page-note">Connexion réservée aux comptes administrateur.</p>

        @if ($errors->any())
            <div class="flash-err">
                @foreach ($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>
            <label class="check-row cursor-pointer">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                <span>Se souvenir de moi</span>
            </label>
            <button type="submit">Se connecter</button>
        </form>
    </div>
@endsection
