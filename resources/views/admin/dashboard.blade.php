@extends('layouts.admin', ['title' => 'Tableau de bord'])

@section('admin_content')
    <h1>Tableau de bord</h1>
    <div class="stats">
        <div class="stat"><strong>{{ $total }}</strong><span>Total billets</span></div>
        <div class="stat"><strong>{{ $paid }}</strong><span>Payés</span></div>
        <div class="stat"><strong>{{ $used }}</strong><span>Utilisés</span></div>
        <div class="stat"><strong>{{ $pending }}</strong><span>En attente</span></div>
    </div>
    <p><a href="{{ route('admin.tickets') }}">Voir tous les tickets</a> · <a href="{{ route('admin.scan') }}">Scanner un billet</a></p>
@endsection
