<?php

namespace Tests\Feature\Products;

use App\Enums\StateProductEnum;
use App\Livewire\Products\IndexProducts;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductActivateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_activates_an_out_of_stock_product_to_available(): void
    {
        $product = Product::query()->create([
            'name' => 'Producto Sin Stock',
            'description' => 'Descripcion',
            'status' => StateProductEnum::OUT_OF_STOCK,
        ]);

        Livewire::test(IndexProducts::class)
            ->call('openDeactivateModal', $product->id, $product->name, 'activate')
            ->assertSet('productActionType', 'activate')
            ->call('confirmAction')
            ->assertSet('isDeactivateModalVisible', false)
            ->assertSee('Producto actualizado de estado correctamente.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => StateProductEnum::AVAILABLE->value,
        ]);
    }

    public function test_it_activates_a_discontinued_product_to_available(): void
    {
        $product = Product::query()->create([
            'name' => 'Producto Descontinuado',
            'description' => 'Descripcion',
            'status' => StateProductEnum::DISCONTINUED,
        ]);

        Livewire::test(IndexProducts::class)
            ->call('openDeactivateModal', $product->id, $product->name, 'activate')
            ->assertSet('productActionType', 'activate')
            ->call('confirmAction')
            ->assertSet('isDeactivateModalVisible', false)
            ->assertSee('Producto actualizado de estado correctamente.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => StateProductEnum::AVAILABLE->value,
        ]);
    }
}