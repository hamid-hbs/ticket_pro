<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventPoster extends Model
{
    protected $fillable = [
        'event_id',
        'path',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function url(): string
    {
        return asset('storage/'.$this->path);
    }
}
