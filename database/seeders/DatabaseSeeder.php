<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Ticket::query()->delete();
        Event::query()->delete();

        Event::create([
            'title' => 'Concert Ticket Pro Live',
            'price' => 5000,
            'date' => now()->addMonth()->toDateString(),
            'location' => 'Dakar Arena',
        ]);

        User::query()->where('email', 'admin@example.com')->delete();

        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);
    }
}
