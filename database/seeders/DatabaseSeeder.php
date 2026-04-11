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
        User::query()->whereIn('email', ['admin@example.com', 'superadmin@example.com'])->delete();

        Event::create([
            'title' => 'IG PARTY 9.0',
            'price' => 3500,
            'date' => now()->addMonth()->toDateString(),
            'location' => 'GLORY PALACE HÔTEL',
        ]);

        User::create([
            'name' => 'Administrateur',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'Super administrateur',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_superadmin' => true,
        ]);
    }
}
