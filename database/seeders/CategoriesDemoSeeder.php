<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesDemoSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'CRM', 'description' => 'Soluciones de gestion comercial y clientes'],
            ['name' => 'ERP', 'description' => 'Gestion administrativa y financiera'],
            ['name' => 'Marketing', 'description' => 'Automatizacion y campañas de marketing'],
            ['name' => 'Soporte', 'description' => 'Mesa de ayuda y gestion de tickets'],
            ['name' => 'Analitica', 'description' => 'Tableros, metricas y reporteria'],
            ['name' => 'Integraciones', 'description' => 'Conectores con servicios de terceros'],
            ['name' => 'Ecommerce', 'description' => 'Ventas online y catalogos digitales'],
            ['name' => 'Recursos Humanos', 'description' => 'Gestion de talento y procesos de personal'],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']],
            );
        }
    }
}
