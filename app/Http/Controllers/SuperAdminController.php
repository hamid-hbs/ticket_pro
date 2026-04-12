<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SuperAdminController extends Controller
{
    public function users(Request $request)
    {
        $query = User::query()
            ->select(['id', 'name', 'email', 'is_admin', 'is_superadmin', 'created_at'])
            ->orderBy('name');

        if ($request->filled('q')) {
            $search = '%'.$request->string('q')->toString().'%';
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        $users = $query->paginate(20)->onEachSide(2)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function createUser()
    {
        $user = new User([
            'is_admin' => false,
            'is_superadmin' => false,
        ]);

        return view('admin.user-form', [
            'user' => $user,
            'action' => route('admin.users.store'),
            'method' => 'POST',
            'buttonLabel' => 'Créer l\'utilisateur',
        ]);
    }

    public function storeUser(Request $request)
    {
        $data = $this->validateUser($request);
        User::create($data);

        return redirect()
            ->route('admin.users')
            ->with('status', 'Utilisateur créé.');
    }

    public function editUser(User $user)
    {
        return view('admin.user-form', [
            'user' => $user,
            'action' => route('admin.users.update', $user),
            'method' => 'PUT',
            'buttonLabel' => 'Enregistrer les modifications',
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $this->validateUser($request, $user);

        if ($user->id === $request->user()->id && $user->isSuperAdmin() && ! $data['is_superadmin']) {
            throw ValidationException::withMessages([
                'is_superadmin' => 'Vous ne pouvez pas vous retirer le rôle superadmin tant que vous êtes le seul superadmin.',
            ]);
        }

        if ($user->isSuperAdmin() && ! $data['is_superadmin']) {
            $remainingSuperadmins = User::query()
                ->where('is_superadmin', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($remainingSuperadmins === 0) {
                throw ValidationException::withMessages([
                    'is_superadmin' => 'Au moins un superadmin doit rester actif.',
                ]);
            }
        }

        if (! array_key_exists('password', $data)) {
            unset($data['password']);
        }

        $user->fill($data);
        $user->save();

        return redirect()
            ->route('admin.users')
            ->with('status', 'Utilisateur modifié.');
    }

    public function destroyUser(User $user)
    {
        $currentUser = request()->user();

        if ($currentUser && $currentUser->id === $user->id) {
            throw ValidationException::withMessages([
                'email' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ]);
        }

        if ($user->isSuperAdmin()) {
            $remainingSuperadmins = User::query()
                ->where('is_superadmin', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($remainingSuperadmins === 0) {
                throw ValidationException::withMessages([
                    'email' => 'Au moins un superadmin doit rester actif.',
                ]);
            }
        }

        $user->delete();

        return redirect()
            ->route('admin.users')
            ->with('status', 'Utilisateur supprimé.');
    }

    public function createTicket()
    {
        $events = Event::query()->orderBy('date')->get();

        if ($events->isEmpty()) {
            return redirect()
                ->route('admin.tickets')
                ->with('status', 'Créez d\'abord un événement avant d\'ajouter un ticket.');
        }

        $ticket = new Ticket([
            'status' => 'paid',
            'qr_code' => (string) Str::uuid(),
            'event_id' => $events->first()->id,
        ]);

        return view('admin.ticket-form', [
            'ticket' => $ticket,
            'events' => $events,
            'action' => route('admin.tickets.store'),
            'method' => 'POST',
            'buttonLabel' => 'Créer le ticket',
        ]);
    }

    public function storeTicket(Request $request)
    {
        $data = $this->validateTicket($request);

        if (empty($data['qr_code'])) {
            $data['qr_code'] = (string) Str::uuid();
        }

        $data['used_at'] = $data['status'] === 'used' ? now() : null;

        if (Schema::hasColumn('tickets', 'used_by_user_id')) {
            $data['used_by_user_id'] = $data['status'] === 'used' ? $request->user()?->id : null;
        } else {
            unset($data['used_by_user_id']);
        }

        Ticket::create($data);

        return redirect()
            ->route('admin.tickets')
            ->with('status', 'Ticket créé.');
    }

    public function editTicket(Ticket $ticket)
    {
        $events = Event::query()->orderBy('date')->get();

        return view('admin.ticket-form', [
            'ticket' => $ticket->load('event'),
            'events' => $events,
            'action' => route('admin.tickets.update', $ticket),
            'method' => 'PUT',
            'buttonLabel' => 'Enregistrer les modifications',
        ]);
    }

    public function updateTicket(Request $request, Ticket $ticket)
    {
        $data = $this->validateTicket($request, $ticket);
        $data['used_at'] = $data['status'] === 'used' ? ($ticket->used_at ?? now()) : null;

        if (Schema::hasColumn('tickets', 'used_by_user_id')) {
            $data['used_by_user_id'] = $data['status'] === 'used' ? ($ticket->used_by_user_id ?? $request->user()?->id) : null;
        } else {
            unset($data['used_by_user_id']);
        }

        $ticket->update($data);

        return redirect()
            ->route('admin.tickets')
            ->with('status', 'Ticket modifié.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'max:255'],
        ]);

        $data['is_admin'] = $request->boolean('is_admin') || $request->boolean('is_superadmin');
        $data['is_superadmin'] = $request->boolean('is_superadmin');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }

    private function validateTicket(Request $request, ?Ticket $ticket = null): array
    {
        $data = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email:rfc,dns', 'max:255'],
                'event_id' => ['required', 'integer', 'exists:events,id'],
                'status' => ['required', Rule::in(['paid', 'used'])],
                'qr_code' => [
                    'required',
                    'string',
                    'max:512',
                    Rule::unique('tickets', 'qr_code')->ignore($ticket?->id),
                ],
                'payment_reference' => ['nullable', 'string', 'max:255'],
            ],
            [
                'email.email' => 'L\'adresse email doit être valide (le domaine email doit exister).',
            ]
        );

        if (empty($data['payment_reference'])) {
            unset($data['payment_reference']);
        }

        return $data;
    }
}
