<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'name',
        'email',
        'qr_code',
        'event_id',
        'payment_reference',
        'status',
        'used_at',
        'email_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'used_at' => 'datetime',
            'email_sent_at' => 'datetime',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}