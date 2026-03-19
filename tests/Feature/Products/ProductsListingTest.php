<?php

namespace Tests\Feature\Products;

use App\Models\Category;
use App\Enums\StateProductEnum;
use App\Livewire\Products\IndexProducts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ProductsListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_empty_state_when_there_are_no_products(): void
    {
        Livewire::test(IndexProducts::class)
            ->assertSee('No hay productos registrados');
    }

    public function test_it_paginates_products_by_ten_records(): void
    {
        $this->seedProducts(11);

        Livewire::test(IndexProducts::class)
            ->assertSee('Producto 11')
            ->assertDontSee('Producto 01')
            ->call('gotoPage', 2)
            ->assertSee('Producto 01')
            ->assertDontSee('Producto 11');
    }

    public function test_it_displays_required_columns_on_the_listing(): void
    {
        $category = Category::create([
            'name' => 'ERP',
            'description' => 'Categoria ERP',
        ]);

        DB::table('products')->insert([
            'name' => 'CRM Core',
            'description' => 'Descripcion de prueba',
            'category_id' => $category->id,
            'stock' => 3,
            'status' => StateProductEnum::AVAILABLE->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        Livewire::test(IndexProducts::class)
            ->assertSee('Nombre')
            ->assertSee('Categoria')
            ->assertSee('Estado')
            ->assertSee('CRM Core')
            ->assertSee('ERP');
    }

    public function test_it_filters_by_name_when_search_has_three_or_more_characters(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'CRM Pro',
                'description' => 'Producto uno',
                'stock' => 5,
                'status' => StateProductEnum::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'name' => 'Help Desk',
                'description' => 'Producto dos',
                'stock' => 2,
                'status' => StateProductEnum::OUT_OF_STOCK->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ]);

        Livewire::test(IndexProducts::class)
            ->set('search', 'crm')
            ->assertSee('CRM Pro')
            ->assertDontSee('Help Desk');
    }

    public function test_it_does_not_filter_when_search_has_less_than_three_characters(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'CRM Basic',
                'description' => 'Producto uno',
                'stock' => 5,
                'status' => StateProductEnum::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'name' => 'Service Plus',
                'description' => 'Producto dos',
                'stock' => 2,
                'status' => StateProductEnum::OUT_OF_STOCK->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ]);

        Livewire::test(IndexProducts::class)
            ->set('search', 'cr')
            ->assertSee('CRM Basic')
            ->assertSee('Service Plus');
    }

    public function test_it_filters_products_by_status_and_allows_all_statuses(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Producto Disponible',
                'description' => 'Producto uno',
                'stock' => 5,
                'status' => StateProductEnum::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'name' => 'Producto Sin Stock',
                'description' => 'Producto dos',
                'stock' => 0,
                'status' => StateProductEnum::OUT_OF_STOCK->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ]);

        Livewire::test(IndexProducts::class)
            ->set('statusFilter', StateProductEnum::AVAILABLE->value)
            ->assertSee('Producto Disponible')
            ->assertDontSee('Producto Sin Stock');

        Livewire::test(IndexProducts::class)
            ->set('statusFilter', StateProductEnum::OUT_OF_STOCK->value)
            ->assertSee('Producto Sin Stock')
            ->assertDontSee('Producto Disponible');

        Livewire::test(IndexProducts::class)
            ->set('statusFilter', '')
            ->assertSee('Producto Disponible')
            ->assertSee('Producto Sin Stock');
    }

    public function test_it_filters_products_by_category_and_combines_filters(): void
    {
        $crmCategory = Category::create([
            'name' => 'CRM',
            'description' => 'Categoria CRM',
        ]);

        $erpCategory = Category::create([
            'name' => 'ERP',
            'description' => 'Categoria ERP',
        ]);

        DB::table('products')->insert([
            [
                'name' => 'CRM Enterprise',
                'description' => 'Producto uno',
                'category_id' => $crmCategory->id,
                'stock' => 5,
                'status' => StateProductEnum::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'name' => 'ERP Suite',
                'description' => 'Producto dos',
                'category_id' => $erpCategory->id,
                'stock' => 0,
                'status' => StateProductEnum::OUT_OF_STOCK->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ]);

        Livewire::test(IndexProducts::class)
            ->set('categoryFilter', (string) $crmCategory->id)
            ->assertSee('CRM Enterprise')
            ->assertDontSee('ERP Suite');

        Livewire::test(IndexProducts::class)
            ->set('search', 'crm')
            ->set('statusFilter', StateProductEnum::AVAILABLE->value)
            ->set('categoryFilter', (string) $crmCategory->id)
            ->assertSee('CRM Enterprise')
            ->assertDontSee('ERP Suite');
    }

    public function test_it_resets_pagination_when_filters_change(): void
    {
        $this->seedProducts(11);

        Livewire::test(IndexProducts::class)
            ->call('gotoPage', 2)
            ->assertSee('Producto 01')
            ->set('search', 'Producto 11')
            ->assertSee('Producto 11')
            ->assertDontSee('Producto 01');
    }

    private function seedProducts(int $total): void
    {
        $records = [];

        for ($i = 1; $i <= $total; $i++) {
            $records[] = [
                'name' => 'Producto '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'description' => 'Descripcion del producto '.$i,
                'stock' => $i,
                'status' => StateProductEnum::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ];
        }

        DB::table('products')->insert($records);
    }
}
