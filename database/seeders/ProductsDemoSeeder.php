<?php

namespace Database\Seeders;

use App\Enums\StateProductEnum;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsDemoSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $productsByCategory = [
            'CRM' => [
                ['name' => 'CRM Core', 'price' => 120, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'CRM Growth', 'price' => 220, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'CRM Enterprise', 'price' => 450, 'status' => StateProductEnum::OUT_OF_STOCK],
            ],
            'ERP' => [
                ['name' => 'ERP Finance', 'price' => 300, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'ERP Supply', 'price' => 340, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'ERP Legacy', 'price' => 180, 'status' => StateProductEnum::DISCONTINUED],
            ],
            'Marketing' => [
                ['name' => 'Automation Studio', 'price' => 140, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'Email Campaigns', 'price' => 95, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'Lead Scoring', 'price' => 115, 'status' => StateProductEnum::OUT_OF_STOCK],
            ],
            'Soporte' => [
                ['name' => 'Helpdesk Base', 'price' => 80, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'Helpdesk Pro', 'price' => 135, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'Onsite Support', 'price' => 260, 'status' => StateProductEnum::DISCONTINUED],
            ],
            'Analitica' => [
                ['name' => 'BI Dashboards', 'price' => 190, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'Revenue Insights', 'price' => 210, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'Forecast Engine', 'price' => 280, 'status' => StateProductEnum::OUT_OF_STOCK],
            ],
            'Integraciones' => [
                ['name' => 'API Gateway', 'price' => 160, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'Webhook Manager', 'price' => 130, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'Legacy Bridge', 'price' => 175, 'status' => StateProductEnum::DISCONTINUED],
            ],
            'Ecommerce' => [
                ['name' => 'Store Manager', 'price' => 150, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'Checkout Plus', 'price' => 170, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'Catalog Booster', 'price' => 110, 'status' => StateProductEnum::OUT_OF_STOCK],
            ],
            'Recursos Humanos' => [
                ['name' => 'People Core', 'price' => 100, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'Performance Hub', 'price' => 145, 'status' => StateProductEnum::AVAILABLE],
                ['name' => 'Payroll Sync', 'price' => 190, 'status' => StateProductEnum::OUT_OF_STOCK],
            ],
        ];

        foreach ($productsByCategory as $categoryName => $products) {
            $category = Category::query()->where('name', $categoryName)->first();

            if (! $category) {
                continue;
            }

            foreach ($products as $product) {
                Product::query()->updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'name' => $product['name'],
                    ],
                    [
                        'description' => 'Producto demo para pruebas funcionales del CRM.',
                        'unit_price' => $product['price'],
                        'status' => $product['status'],
                    ],
                );
            }
        }
    }
}
