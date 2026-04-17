<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPurchase extends Model
{
    protected $fillable = [
        'name',
        'email',
        'event_id',
        'user_id',
        'amount',
        'status',
        'payment_reference',
        'ticket_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'ticket_id' => 'integer',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}