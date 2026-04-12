<?php

namespace App\Http\Controllers;

use App\Mail\TicketPurchasedMail;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $query = Ticket::whereIn('status', ['paid', 'used']);

        $total = (clone $query)->count();
        $paid = (clone $query)->where('status', 'paid')->count();
        $used = (clone $query)->where('status', 'used')->count();

        return view('admin.dashboard', compact('total', 'paid', 'used'));
    }

    public function sellForm()
    {
        $event = Event::query()->orderBy('date')->first();

        if (! $event) {
            return response('Aucun événement configuré. Exécutez les seeders ou créez un événement en base.', 500);
        }

        return view('admin.sell', compact('event'));
    }

    public function sellStore(Request $request)
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
            'status' => 'paid',
        ]);

        if (Schema::hasColumn('tickets', 'sold_by_user_id')) {
            $ticket->sold_by_user_id = $request->user()?->id;
            $ticket->save();
        }

        Mail::to($ticket->email)->send(new TicketPurchasedMail($ticket->fresh()));

        $ticket->email_sent_at = now();
        $ticket->save();

        return redirect()
            ->route('admin.sell')
            ->with('status', 'Billet vendu et email envoyé.');
    }

    public function tickets(Request $request)
    {
        $query = Ticket::with(['event', 'soldBy'])->orderByDesc('created_at');
        $allowedStatuses = ['paid', 'used'];

        $query->whereIn('status', $allowedStatuses);

        if ($request->filled('status') && in_array($request->string('status')->toString(), $allowedStatuses, true)) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('q')) {
            $q = '%'.$request->string('q').'%';
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', $q)
                    ->orWhere('email', 'like', $q)
                    ->orWhere('qr_code', 'like', $q);
            });
        }

        $tickets = $query->paginate(20)->withQueryString();

        return view('admin.tickets', compact('tickets'));
    }

    public function showTicket(Ticket $ticket)
    {
        $ticket->load(['event', 'soldBy', 'scannedBy']);

        return view('admin.ticket-show', compact('ticket'));
    }

    public function destroyTicket(Ticket $ticket, Request $request)
    {
        // Only superadmin can delete tickets
        if (!$request->user()?->isSuperAdmin()) {
            abort(403);
        }

        $ticket->delete();

        return redirect()
            ->route('admin.tickets')
            ->with('status', 'Ticket supprimé.');
    }

    public function scanForm()
    {
        return view('admin.scan');
    }

    public function scan(Request $request)
    {
        $data = $request->validate([
            'qr_code' => ['required', 'string', 'max:512'],
        ]);

        $raw = trim($data['qr_code']);
        $ticket = Ticket::with('event')->where('qr_code', $raw)->first();

        if (! $ticket) {
            return back()
                ->withInput()
                ->with('scan_result', ['ok' => false, 'message' => 'Ticket invalide ou inconnu.']);
        }

        if ($ticket->status === 'used') {
            return back()
                ->withInput()
                ->with('scan_result', [
                    'ok' => false,
                    'message' => 'Accès refusé : ce billet a déjà été utilisé.',
                    'ticket' => $ticket,
                ]);
        }

        if ($ticket->status !== 'paid') {
            return back()
                ->withInput()
                ->with('scan_result', [
                    'ok' => false,
                    'message' => 'Accès refusé : paiement non validé.',
                    'ticket' => $ticket,
                ]);
        }

        $ticket->status = 'used';
        $ticket->used_at = now();

        if (Schema::hasColumn('tickets', 'used_by_user_id')) {
            $ticket->used_by_user_id = $request->user()?->id;
        }

        $ticket->save();

        return back()->with('scan_result', [
            'ok' => true,
            'message' => 'Accès autorisé.',
            'ticket' => $ticket->fresh(),
        ]);
    }
}
