@extends('layouts.app')

@section('title', 'Paiement')

@section('content')
    <div class="card card--narrow">
        <h1>Paiement</h1>
        <p class="page-note"><strong>{{ $purchase->name }}</strong> — {{ $purchase->email }}</p>

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
        <p id="payment-message" class="page-note" style="margin-top:0.8rem;display:none;"></p>
    </div>

    <script src="https://cdn.fedapay.com/checkout.js?v=1.1.7"></script>
    <script>
    (function () {
        var payButton = document.getElementById('pay-btn');
        var paymentMessage = document.getElementById('payment-message');
        var callbackUrl = @json(route('payment.callback'));
        var successBase = @json(url('/success'));
        var csrfToken = @json(csrf_token());
        var purchaseId = @json($purchase->id);
        var isOpening = false;

        function showMessage(text, isError) {
            if (!paymentMessage) {
                return;
            }

            paymentMessage.textContent = text;
            paymentMessage.style.display = 'block';
            paymentMessage.style.color = isError ? '#B91C1C' : '#065F46';
        }

        function forceFullscreenWidget() {
            var overlays = document.querySelectorAll('[class*="fedapay"], [id*="fedapay"], iframe[src*="fedapay"]');

            overlays.forEach(function (element) {
                var tag = (element.tagName || '').toUpperCase();

                if (tag === 'IFRAME') {
                    element.style.position = 'fixed';
                    element.style.inset = '0';
                    element.style.width = '100vw';
                    element.style.height = '100vh';
                    element.style.maxWidth = '100vw';
                    element.style.maxHeight = '100vh';
                    element.style.zIndex = '2147483647';
                    element.style.border = '0';
                }
            });
        }

        function openPaymentWidget() {
            if (isOpening) {
                return;
            }

            isOpening = true;
            showMessage('Ouverture du paiement...', false);

            try {
                paymentWidget.open();
                window.setTimeout(forceFullscreenWidget, 100);
                window.setTimeout(forceFullscreenWidget, 350);
                window.setTimeout(forceFullscreenWidget, 700);
            } catch (error) {
                showMessage('Impossible d\'ouvrir FedaPay. Rafraichis la page puis reessaie.', true);
            } finally {
                window.setTimeout(function () {
                    isOpening = false;
                }, 900);
            }
        }

        var paymentWidget = FedaPay.init({
            public_key: @json(config('services.fedapay.public_key')),
            environment: @json(config('services.fedapay.environment')),
            transaction: {
                amount: 100,
                description: @json('Billet pour '.$event->title),
                custom_metadata: {
                    purchase_id: @json($purchase->id),
                    ticket_id: @json($purchase->id),
                    event_id: @json($event->id)
                }
            },
            customer: {
                email: @json($purchase->email),
                firstname: @json(explode(' ', trim($purchase->name))[0] ?? $purchase->name),
                lastname: @json(trim(preg_replace('/^\S+\s*/', '', trim($purchase->name))) ?: $purchase->name)
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
                var paidStatuses = ['SUCCESS', 'PAID', 'APPROVED', 'COMPLETED', 'SUCCEEDED', 'SUCCESSFUL'];
                var isPaid = paidStatuses.indexOf(status) !== -1;

                if (!transactionId) {
                    transactionId = 'fedapay_' + purchaseId + '_' + Date.now();
                }

                if (!isPaid) {
                    showMessage('Paiement non confirme. Verifie le statut dans FedaPay puis reessaie.', true);
                    return;
                }

                showMessage('Paiement confirme, validation du billet...', false);

                fetch(callbackUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        purchase_id: purchaseId,
                        ticket_id: purchaseId,
                        status: status,
                        transaction_id: transactionId,
                        transaction: transaction,
                        paid: isPaid
                    })
                }).then(function (response) {
                    if (!response.ok) {
                        return response.json().catch(function () {
                            return { message: 'Erreur de validation du paiement.' };
                        }).then(function (payload) {
                            throw new Error(payload.message || 'Erreur de validation du paiement.');
                        });
                    }

                    return response.json();
                }).then(function (data) {
                    if (data && data.ticket_id) {
                        window.location.href = successBase + '/' + data.ticket_id;
                        return;
                    }

                    throw new Error('Ticket non cree apres paiement.');
                }).catch(function (error) {
                    showMessage((error && error.message) || 'Transaction detectee mais non validee cote serveur.', true);
                });
            }
        });

        payButton.addEventListener('click', function () {
            openPaymentWidget();
        });

        window.addEventListener('load', function () {
            window.setTimeout(openPaymentWidget, 180);
        });
    })();
    </script>
@endsection
