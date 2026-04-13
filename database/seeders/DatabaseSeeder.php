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
     
        $users = [
    ['name' => 'Hamid SuperAdmin', 'email' => 'hamid@superadmin.com', 'is_superadmin' => true],
    ['name' => 'Hamid', 'email' => 'hamid@admin.com'],
    ['name' => 'Albéric', 'email' => 'alberic@admin.com'],
    ['name' => 'Magali', 'email' => 'magali@admin.com'],
    ['name' => 'Absath', 'email' => 'absath@admin.com'],
    ['name' => 'Archange', 'email' => 'archange@admin.com'],
    ['name' => 'Armel', 'email' => 'armel@admin.com'],
    ['name' => 'Caleb', 'email' => 'caleb@admin.com'],
    ['name' => 'Cathia', 'email' => 'cathia@admin.com'],
    ['name' => 'Chadrac', 'email' => 'chadrac@admin.com'],
    ['name' => 'Darren', 'email' => 'darren@admin.com'],
    ['name' => 'Ségolène', 'email' => 'segolene@admin.com'],
    ['name' => 'Dilaal', 'email' => 'dilaal@admin.com'],
    ['name' => 'Emèth', 'email' => 'emeth@admin.com'],
    ['name' => 'Espoir', 'email' => 'espoir@admin.com'],
    ['name' => 'Eunice', 'email' => 'eunice@admin.com'],
    ['name' => 'Ezéchiel', 'email' => 'ezechiel@admin.com'],
    ['name' => 'Falone', 'email' => 'falone@admin.com'],
    ['name' => 'Farnèse', 'email' => 'farnese@admin.com'],
    ['name' => 'Gabriel', 'email' => 'gabriel@admin.com'],
    ['name' => 'Ichola', 'email' => 'ichola@admin.com'],
    ['name' => 'Loïc', 'email' => 'loic@admin.com'],
    ['name' => 'Mariko', 'email' => 'mariko@admin.com'],
    ['name' => 'Marlène', 'email' => 'marlene@admin.com'],
    ['name' => 'Melvine', 'email' => 'melvine@admin.com'],
    ['name' => 'Miracle', 'email' => 'miracle@admin.com'],
    ['name' => 'Mystica', 'email' => 'mystica@admin.com'],
    ['name' => 'Rayane', 'email' => 'rayane@admin.com'],
    ['name' => 'Océane', 'email' => 'oceane@admin.com'],
    ['name' => 'Paris', 'email' => 'paris@admin.com'],
    ['name' => 'Ryan', 'email' => 'ryan@admin.com'],
    ['name' => 'Soboure', 'email' => 'soboure@admin.com'],
    ['name' => 'Stély', 'email' => 'stely@admin.com'],
    ['name' => 'Yanees', 'email' => 'yanees@admin.com'],
    ['name' => 'Yu-on', 'email' => 'yuon@admin.com'],
];

foreach ($users as $user) {
    User::updateOrCreate(
        ['email' => $user['email']],
        array_merge([
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_superadmin' => $user['is_superadmin'] ?? false,
        ], $user)
    );
}
    }
}
