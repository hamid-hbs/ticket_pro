<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'price',
        'date',
        'start_time',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function posterUrl(): ?string
    {
        $poster = $this->posters->sortBy('sort_order')->first();

        if ($poster) {
            return $poster->url();
        }

        return $this->poster_path ? asset('storage/'.$this->poster_path) : null;
    }

    public function posters(): HasMany
    {
        return $this->hasMany(EventPoster::class)->orderBy('sort_order');
    }
}

