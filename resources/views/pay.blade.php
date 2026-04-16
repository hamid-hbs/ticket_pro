@extends('layouts.app')

@section('title', 'Paiement')

@section('content')
    <div class="card card--narrow">
        <h1>Paiement</h1>
        <p class="page-note"><strong>{{ $ticket->name }}</strong> — {{ $ticket->email }}</p>

        <div class="summary">
            <div>
                <div class="muted">Montant</div>
                <strong>100 FCFA</strong>
            </div>
            <div>
                <div class="muted">Événement</div>
                <strong>{{ $event->title }}</strong>
            </div>
        </div>

        <div class="actions">
            <button type="button" id="pay-btn">Payer avec FedaPay</button>
        </div>
    </div>

    <script src="https://cdn.fedapay.com/checkout.js?v=1.1.7"></script>
    <script>
    (function () {
        var payButton = document.getElementById('pay-btn');
        var callbackUrl = @json(route('payment.callback'));
        var successUrl = @json(url('/success/'.$ticket->id));
        var csrfToken = @json(csrf_token());
        var ticketId = @json($ticket->id);
        var paymentWidget = FedaPay.init({
            public_key: @json(config('services.fedapay.public_key')),
            environment: @json(config('services.fedapay.environment')),
            transaction: {
                amount: 100,
                description: @json('Billet pour '.$event->title),
                custom_metadata: {
                    ticket_id: @json($ticket->id),
                    event_id: @json($event->id)
                }
            },
            customer: {
                email: @json($ticket->email),
                firstname: @json(explode(' ', trim($ticket->name))[0] ?? $ticket->name),
                lastname: @json(trim(preg_replace('/^\S+\s*/', '', trim($ticket->name))) ?: $ticket->name)
            },
            onComplete: function (response) {
                var transaction = response && (response.transaction || response.data || response);
                var status = String(
                    (transaction && transaction.status) || response.status || response.state || ''
                ).toUpperCase();
                var transactionId =
                    (transaction && (transaction.id || transaction.transaction_id)) ||
                    response.transaction_id ||
                    response.id ||
                    '';

                if (!transactionId) {
                    transactionId = 'fedapay_' + ticketId + '_' + Date.now();
                }

                if (['SUCCESS', 'PAID', 'APPROVED', 'COMPLETED'].indexOf(status) === -1) {
                    return;
                }

                fetch(callbackUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ticket_id: ticketId,
                        status: status,
                        transaction_id: transactionId,
                        transaction: transaction,
                        paid: true
                    })
                }).then(function () {
                    window.location.href = successUrl;
                }).catch(function () {
                    window.location.href = successUrl;
                });
            }
        });

        payButton.addEventListener('click', function () {
            paymentWidget.open();
        });
    })();
    </script>
@endsection
