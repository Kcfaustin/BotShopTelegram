<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Pack Premium Telegram Bots',
                'slug' => 'pack-premium',
                'description' => "Scripts prets a l'emploi pour lancer des bots e-commerce Telegram.",
                'price' => 15000,
                'currency' => 'XOF',
                'file_path' => 'products/pack-premium.zip',
                'file_disk' => 'local',
            ],
            [
                'name' => 'Guide Automation + Templates',
                'slug' => 'guide-automation',
                'description' => 'Guide PDF + templates Notion pour gerer tes ventes automatiquement.',
                'price' => 8000,
                'currency' => 'XOF',
                'file_path' => 'products/guide-automation.pdf',
                'file_disk' => 'local',
            ],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
