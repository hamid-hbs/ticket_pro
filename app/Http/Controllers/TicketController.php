<?php

namespace App\Http\Controllers;

use App\Mail\TicketPurchasedMail;
use App\Models\Ticket;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index()
    {
        $event = Event::first();

        if (! $event) {
            return response('Aucun événement configuré. Exécutez les seeders ou créez un événement en base.', 500);
        }

        return view('buy', compact('event'));
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

        $ticket = Ticket::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'event_id' => $validated['event_id'],
            'qr_code' => (string) Str::uuid(),
        ]);

        return redirect('/pay/'.$ticket->id);
    }

    public function pay($id)
    {
        $ticket = Ticket::findOrFail($id);
        $event = $ticket->event;

        return view('pay', compact('ticket', 'event'));
    }

    public function callback(Request $request)
    {
        $ticketId = data_get($request->all(), 'data.ticket_id');
        $status = (string) data_get($request->all(), 'status', '');
        $transactionId = data_get($request->all(), 'transaction_id');

        if (! $ticketId) {
            return response()->json(['message' => 'ticket_id manquant'], 422);
        }

        $ticket = Ticket::find($ticketId);

        if (! $ticket) {
            return response()->json(['message' => 'Ticket introuvable'], 404);
        }

        if (strtoupper($status) === 'SUCCESS') {
            if ($ticket->status !== 'paid') {
                $ticket->status = 'paid';
                $ticket->payment_reference = $transactionId;
                $ticket->save();
                $this->notifyPurchase($ticket->fresh());
            }
        }

        return response()->json(['message' => 'OK']);
    }

    public function sandboxPay(Ticket $ticket)
    {
        abort_unless((bool) config('services.kkiapay.sandbox'), 403, 'Sandbox désactivé.');

        if ($ticket->status !== 'paid') {
            $ticket->status = 'paid';
            $ticket->payment_reference = 'sandbox_'.Str::uuid();
            $ticket->save();
            $this->notifyPurchase($ticket->fresh());
        }

        return redirect('/success/'.$ticket->id);
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
}
