<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création d'un administrateur
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'System',
            'email' => 'admin@system.com',
            'password' => Hash::make('devdevdev'),
            'role' => 'admin',
            'status' => 'active',
            'logo' => 'https://via.placeholder.com/150'
        ]);

        // Création d'un utilisateur standard
        User::create([
            'first_name' => 'Justin',
            'last_name' => 'KOUDOSSOU',
            'email' => 'justin@gmail.com',
            'password' => Hash::make('@zerty123'),
            'role' => 'user',
            'status' => 'active',
            'logo' => 'https://via.placeholder.com/150'
        ]);

        // Création de plusieurs utilisateurs avec une boucle
        $users = [
            [
                'first_name' => 'Alice',
                'last_name' => 'Smith',
                'email' => 'alice@example.com',
                'role' => 'user'
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'email' => 'bob@example.com',
                'role' => 'user'
            ],
            [
                'first_name' => 'Claire',
                'last_name' => 'Wilson',
                'email' => 'claire@example.com',
                'role' => 'user'
            ]
        ];

        foreach ($users as $userData) {
            User::create([
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'password' => Hash::make('password123'),
                'role' => $userData['role'],
                'status' => 'active',
                'logo' => 'https://via.placeholder.com/150'
            ]);
        }
    }
}
