@extends('layouts.app')

@section('title', ($title ?? 'Admin').' — '.config('app.name'))

@section('content')
    <nav>
        <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
        <a href="{{ route('admin.tickets') }}">Tickets</a>
        @if(auth()->user()?->isSuperAdmin())
            <a href="{{ route('admin.users') }}">Utilisateurs</a>
            <a href="{{ route('admin.users.create') }}">Nouvel utilisateur</a>
            <a href="{{ route('admin.tickets.create') }}">Nouveau ticket</a>
        @endif
        <a href="{{ route('admin.scan') }}">Scanner</a>
        <span class="flex-spacer"></span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="danger" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?')">Déconnexion</button>
        </form>
    </nav>
    @yield('admin_content')
@endsection
