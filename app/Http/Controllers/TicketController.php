<?php

namespace App\Http\Controllers;

use App\Mail\TicketPurchasedMail;
use App\Models\Ticket;
use App\Models\TicketPurchase;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class TicketController extends Controller
{
    public function index()
    {
        $events = Event::query()->orderBy('date')->orderBy('start_time')->orderBy('title')->get();

        if ($events->isEmpty()) {
            return response('Aucun événement configuré. Exécutez les seeders ou créez un événement en base.', 500);
        }

        return view('buy', compact('events'));
    }

    public function buy(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email:rfc,dns', 'max:255'],
                'event_id' => ['required', 'integer', 'exists:events,id'],
            ],
            [
                'email.email' => 'L\'adresse email doit être valide (le domaine email doit exister).',
            ]
        );

        $purchase = TicketPurchase::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'event_id' => $validated['event_id'],
            'user_id' => $request->user()?->id,
            'amount' => (int) Event::findOrFail($validated['event_id'])->price,
            'status' => 'pending',
        ]);

        return redirect('/pay/'.$purchase->id);
    }

    public function mine(Request $request)
    {
        $tickets = Ticket::query()
            ->with(['event.posters'])
            ->where('buyer_user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $tickets->each(function (Ticket $ticket): void {
            $ticket->qr_data_uri = $this->buildQrDataUri($ticket->qr_code);
        });

        return view('tickets.mine', compact('tickets'));
    }

    public function pay(TicketPurchase $purchase)
    {
        $event = $purchase->event;

        return view('pay', compact('purchase', 'event'));
    }

    public function downloadPdf(Request $request, Ticket $ticket)
    {
        abort_unless(
            $ticket->buyer_user_id === $request->user()->id,
            403,
            'Vous ne pouvez télécharger que vos propres billets.'
        );

        $ticket->loadMissing('event');

        $qrDataUri = $this->buildQrDataUri($ticket->qr_code);
        $qrPngBase64 = str_starts_with((string) $qrDataUri, 'data:image/png;base64,')
            ? substr((string) $qrDataUri, strlen('data:image/png;base64,'))
            : null;

        $pdfBinary = Pdf::loadView('pdf.ticket', [
            'ticket' => $ticket,
            'qrPngBase64' => $qrPngBase64,
        ])->output();

        return response()->streamDownload(
            fn () => print($pdfBinary),
            'billet-'.$ticket->id.'.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function callback(Request $request)
    {
        $payload = $request->all();
        $purchaseId = data_get($payload, 'purchase_id')
            ?? data_get($payload, 'ticket_id')
            ?? data_get($payload, 'data.purchase_id')
            ?? data_get($payload, 'data.ticket_id')
            ?? data_get($payload, 'transaction.custom_metadata.purchase_id')
            ?? data_get($payload, 'transaction.custom_metadata.ticket_id')
            ?? data_get($payload, 'custom_metadata.purchase_id')
            ?? data_get($payload, 'custom_metadata.ticket_id');

        $status = strtoupper((string) (data_get($payload, 'status')
            ?? data_get($payload, 'transaction.status')
            ?? data_get($payload, 'data.status')
            ?? ''));

        $transactionId = data_get($payload, 'transaction_id')
            ?? data_get($payload, 'transaction.id')
            ?? data_get($payload, 'data.transaction_id')
            ?? data_get($payload, 'id');

        if (! $purchaseId) {
            return response()->json(['message' => 'ticket_id manquant'], 422);
        }

        $purchase = TicketPurchase::with('event')->find($purchaseId);

        if (! $purchase) {
            return response()->json(['message' => 'Achat introuvable'], 404);
        }

        if (in_array($status, ['SUCCESS', 'PAID', 'APPROVED', 'COMPLETED'], true) || $request->boolean('paid')) {
            if ($purchase->status !== 'paid') {
                $purchase->status = 'paid';
                $purchase->payment_reference = $transactionId ?: 'fedapay_'.Str::uuid();
                $purchase->save();

                $ticket = $purchase->ticket;

                if (! $ticket) {
                    $ticket = Ticket::create([
                        'name' => $purchase->name,
                        'email' => $purchase->email,
                        'event_id' => $purchase->event_id,
                        'qr_code' => (string) Str::uuid(),
                        'payment_reference' => $purchase->payment_reference,
                        'status' => 'paid',
                        'buyer_user_id' => $purchase->user_id,
                    ]);

                    $purchase->ticket_id = $ticket->id;
                    $purchase->save();
                }

                $this->notifyPurchase($ticket->fresh());

                return response()->json([
                    'message' => 'OK',
                    'ticket_id' => $ticket->id,
                ]);
            }
        }

        return response()->json([
            'message' => 'OK',
            'ticket_id' => $purchase->ticket_id,
        ]);
    }

    public function success($id)
    {
        $ticket = Ticket::with('event')->findOrFail($id);

        return view('success', compact('ticket'));
    }

    /**
     * Envoie l’email de confirmation une seule fois après paiement validé.
     */
    private function notifyPurchase(Ticket $ticket): void
    {
        if ($ticket->email_sent_at !== null) {
            return;
        }

        Mail::to($ticket->email)->send(new TicketPurchasedMail($ticket));

        $ticket->email_sent_at = now();
        $ticket->save();
    }

    private function buildQrDataUri(string $payload): ?string
    {
        try {
            $qrPng = Builder::create()
                ->data($payload)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
                ->size(240)
                ->margin(8)
                ->build()
                ->getString();

            return 'data:image/png;base64,'.base64_encode($qrPng);
        } catch (Throwable) {
            return null;
        }
    }
}
