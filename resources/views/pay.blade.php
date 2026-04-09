@extends('layouts.app')

@section('title', 'Paiement')

@section('content')
    <div class="card" style="max-width:480px;">
        <h1>Paiement</h1>
        <p><strong>{{ $ticket->name }}</strong> — {{ $ticket->email }}</p>
        <p>Montant : <strong>{{ number_format($event->price, 0, ',', ' ') }} FCFA</strong></p>

        <button type="button" onclick="pay()">Payer avec Mobile Money</button>

        @if(config('services.kkiapay.sandbox'))
            <form method="POST" action="{{ route('payment.sandbox.pay', ['ticket' => $ticket->id]) }}" style="margin-top: 12px;">
                @csrf
                <button type="submit" class="secondary">Simuler paiement (sandbox)</button>
            </form>
        @endif
    </div>

    <script src="https://cdn.kkiapay.me/k.js"></script>
    <script>
    function pay(){
        openKkiapayWidget({
            amount: {{ $event->price }},
            position: "center",
            key: "{{ config('services.kkiapay.public_key') }}",
            sandbox: {{ config('services.kkiapay.sandbox') ? 'true' : 'false' }},
            callback: "{{ route('payment.callback') }}",
            data: {
                ticket_id: {{ $ticket->id }}
            }
        })
    }
    </script>
@endsection
