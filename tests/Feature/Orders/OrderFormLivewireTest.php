<?php

namespace Tests\Feature\Orders;

use App\Enums\StateProductEnum;
use App\Livewire\Orders\AddOrder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderFormLivewireTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_adds_and_removes_items_dynamically(): void
    {
        Livewire::test(AddOrder::class)
            ->assertCount('items', 1)
            ->call('addItem')
            ->assertCount('items', 2)
            ->call('removeItem', 1)
            ->assertCount('items', 1)
            ->call('removeItem', 0)
            ->assertCount('items', 1);
    }

    public function test_it_recalculates_subtotals_and_total_in_real_time(): void
    {
        $category = Category::query()->create([
            'name' => 'Realtime Category',
            'description' => 'Categoria para realtime',
        ]);

        $productOne = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Realtime Product 1',
            'description' => 'Producto uno',
            'unit_price' => 12.50,
            'status' => StateProductEnum::AVAILABLE->value,
        ]);

        $productTwo = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Realtime Product 2',
            'description' => 'Producto dos',
            'unit_price' => 5,
            'status' => StateProductEnum::AVAILABLE->value,
        ]);

        Livewire::test(AddOrder::class)
            ->set('items.0.product_id', (string) $productOne->id)
            ->set('items.0.count', 2)
            ->assertSet('items.0.subtotal', '25.00')
            ->assertSet('total', '25.00')
            ->call('addItem')
            ->set('items.1.product_id', (string) $productTwo->id)
            ->set('items.1.count', 3)
            ->assertSet('items.1.subtotal', '15.00')
            ->assertSet('total', '40.00');
    }
}
