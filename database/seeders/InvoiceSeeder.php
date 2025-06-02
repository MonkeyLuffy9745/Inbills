<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        
        foreach ($clients as $client) {
            // Création de 2 factures par client
            for ($i = 0; $i < 2; $i++) {
                $date = Carbon::now()->subDays(rand(1, 30));
                $dueDate = $date->copy()->addDays(30);
                $amount = rand(100000, 1000000); // Montant entre 100,000 et 1,000,000
                
                Invoice::create([
                    'invoice_number' => 'INV-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'invoice_date' => $date->format('Y-m-d'),
                    'invoice_due_date' => $dueDate->format('Y-m-d'),
                    'invoice_amount' => $amount,
                    'invoice_status' => rand(0, 1) ? 'paid' : 'pending',
                    'payement_method' => rand(0, 1) ? 'bank_transfer' : 'cash',
                    'payement_date' => $date->copy()->addDays(rand(1, 15))->format('Y-m-d'),
                    'vat' => 18, // TVA standard au Bénin
                    'currency' => 'EUR',
                    'notes' => 'Facture générée automatiquement',
                    'client_id' => $client->id,
                    'user_id' => $client->user_id
                ]);
            }
        }
    }
}
