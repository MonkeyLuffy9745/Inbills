<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InvoiceItem;
use App\Models\Invoice;

class InvoiceItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Développement Web',
                'description' => 'Développement de site web responsive',
                'price' => 250000
            ],
            [
                'name' => 'Design UI/UX',
                'description' => 'Conception d\'interfaces utilisateur modernes',
                'price' => 150000
            ],
            [
                'name' => 'Maintenance',
                'description' => 'Maintenance mensuelle et mises à jour',
                'price' => 100000
            ],
            [
                'name' => 'Formation',
                'description' => 'Formation sur les outils numériques',
                'price' => 75000
            ],
            [
                'name' => 'Consultation',
                'description' => 'Consultation et conseils en stratégie digitale',
                'price' => 125000
            ]
        ];

        $invoices = Invoice::all();

        foreach ($invoices as $invoice) {
            // Ajout de 2 à 4 items par facture
            $numberOfItems = rand(2, 4);
            $totalAmount = 0;

            for ($i = 0; $i < $numberOfItems; $i++) {
                $service = $services[array_rand($services)];
                $quantity = rand(1, 3);
                $price = $service['price'];
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_name' => $service['name'],
                    'description' => $service['description'],
                    'item_quantity' => $quantity,
                    'item_price' => $price,
                    'vat' => 18 // TVA standard au Bénin
                ]);

                $totalAmount += $quantity * $price;
            }

            // Mise à jour du montant total de la facture
            $invoice->invoice_amount = $totalAmount;
            $invoice->save();
        }
    }
}
