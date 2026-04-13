@extends('layouts.admin', ['title' => 'Tableau de bord'])

@section('admin_content')
    <h1>Tableau de bord</h1>
    <div class="stats">
        <div class="stat"><strong>{{ $total }}</strong><span>Total billets</span></div>
        <div class="stat"><strong>{{ $paid }}</strong><span>Payés</span></div>
        <div class="stat"><strong>{{ $used }}</strong><span>Utilisés</span></div>
        @if(auth()->user()?->isSuperAdmin())
            <div class="stat"><strong>{{ \App\Models\User::count() }}</strong><span>Utilisateurs</span></div>
            <div class="stat"><strong>{{ \App\Models\Event::count() }}</strong><span>Événements</span></div>
        @endif
    </div>
    <p>
        <a href="{{ route('admin.sell') }}">Vendre un billet</a>
        · <a href="{{ route('admin.tickets') }}">Voir tous les tickets</a>
        · <a href="{{ route('admin.scan') }}">Scanner un billet</a>
        @if(auth()->user()?->isSuperAdmin())
            · <a href="{{ route('admin.events') }}">Gérer les événements</a>
            · <a href="{{ route('admin.users') }}">Gérer les utilisateurs</a>
        @endif
    </p>
@endsection
