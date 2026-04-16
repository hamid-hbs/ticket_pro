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
        'sold_by_user_id',
        'used_by_user_id',
        'buyer_user_id',
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

    public function soldBy()
    {
        return $this->belongsTo(User::class, 'sold_by_user_id');
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }
}