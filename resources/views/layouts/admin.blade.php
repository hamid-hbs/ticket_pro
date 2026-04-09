@extends('layouts.app')

@section('title', ($title ?? 'Admin').' — '.config('app.name'))

@section('content')
    <nav>
        <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
        <a href="{{ route('admin.tickets') }}">Tickets</a>
        <a href="{{ route('admin.scan') }}">Scanner</a>
        <span style="flex:1"></span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="secondary" style="font-size:0.875rem;">Déconnexion</button>
        </form>
    </nav>
    @yield('admin_content')
@endsection
