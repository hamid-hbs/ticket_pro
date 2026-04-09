<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $total = Ticket::count();
        $paid = Ticket::where('status', 'paid')->count();
        $used = Ticket::where('status', 'used')->count();
        $pending = Ticket::where('status', 'pending')->count();

        return view('admin.dashboard', compact('total', 'paid', 'used', 'pending'));
    }

    public function tickets(Request $request)
    {
        $query = Ticket::with('event')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
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
        $ticket->load('event');

        return view('admin.ticket-show', compact('ticket'));
    }

    public function destroyTicket(Ticket $ticket)
    {
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
        $ticket->save();

        return back()->with('scan_result', [
            'ok' => true,
            'message' => 'Accès autorisé.',
            'ticket' => $ticket->fresh(),
        ]);
    }
}
