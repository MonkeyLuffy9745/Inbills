<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@system.com')->first();
        $user = User::where('email', 'justin@gmail.com')->first();

        // Clients pour l'administrateur
        $adminClients = [
            [
                'name' => 'Tech Solutions SARL',
                'email' => 'contact@techsolutions.com',
                'phone' => '+229 97123456',
                'address' => '123 Rue du Commerce, Cotonou',
                'user_id' => $admin->id
            ],
            [
                'name' => 'Digital Services Plus',
                'email' => 'info@digitalservices.com',
                'phone' => '+229 95789012',
                'address' => '45 Avenue Jean Paul II, Cotonou',
                'user_id' => $admin->id
            ]
        ];

        // Clients pour l'utilisateur standard
        $userClients = [
            [
                'name' => 'Bénin Innovation Hub',
                'email' => 'contact@bih.bj',
                'phone' => '+229 96234567',
                'address' => '78 Boulevard de la Marina, Cotonou',
                'user_id' => $user->id
            ],
            [
                'name' => 'Africa Web Services',
                'email' => 'info@aws.bj',
                'phone' => '+229 94567890',
                'address' => '156 Rue des Entrepreneurs, Cotonou',
                'user_id' => $user->id
            ],
            [
                'name' => 'Global Trading BJ',
                'email' => 'contact@globaltrading.bj',
                'phone' => '+229 98901234',
                'address' => '34 Avenue du Port, Cotonou',
                'user_id' => $user->id
            ]
        ];

        // Création des clients
        foreach ($adminClients as $clientData) {
            Client::create($clientData);
        }

        foreach ($userClients as $clientData) {
            Client::create($clientData);
        }
    }
}
