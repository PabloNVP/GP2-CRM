<?php

namespace Tests\Feature\Products;

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
        DB::table('products')->insert([
            'name' => 'CRM Core',
            'description' => 'Descripcion de prueba',
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
            ->assertSee('CRM Core');
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
