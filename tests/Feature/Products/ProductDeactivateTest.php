<?php

namespace Tests\Feature\Products;

use App\Enums\StateProductEnum;
use App\Livewire\Products\IndexProducts;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductDeactivateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deactivates_an_available_product_to_out_of_stock(): void
    {
        $product = Product::query()->create([
            'name' => 'Producto Activo',
            'description' => 'Descripcion',
            'status' => StateProductEnum::AVAILABLE,
        ]);

        Livewire::test(IndexProducts::class)
            ->call('openDeactivateModal', $product->id, $product->name)
            ->assertSet('isDeactivateModalVisible', true)
            ->call('confirmAction')
            ->assertSet('isDeactivateModalVisible', false)
            ->assertSee('Producto actualizado de estado correctamente.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => StateProductEnum::OUT_OF_STOCK->value,
        ]);
    }

    public function test_it_marks_out_of_stock_product_as_discontinued_on_second_deactivate(): void
    {
        $product = Product::query()->create([
            'name' => 'Producto Sin Stock',
            'description' => 'Descripcion',
            'status' => StateProductEnum::OUT_OF_STOCK,
        ]);

        Livewire::test(IndexProducts::class)
            ->call('openDeactivateModal', $product->id, $product->name)
            ->call('confirmAction')
            ->assertSet('isDeactivateModalVisible', false);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => StateProductEnum::DISCONTINUED->value,
        ]);
    }
}
