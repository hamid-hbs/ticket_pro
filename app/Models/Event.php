<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'price',
        'date',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}

