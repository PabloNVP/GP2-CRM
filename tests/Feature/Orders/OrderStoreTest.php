<?php

namespace Tests\Feature\Orders;

use App\Enums\StateEnum;
use App\Enums\StateOrderEnum;
use App\Enums\StateProductEnum;
use App\Livewire\Orders\AddOrder;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_an_order_with_multiple_items_successfully(): void
    {
        $clientId = $this->createClient('Nico', 'Martinez', 'nico.order.store@example.com');

        $category = Category::query()->create([
            'name' => 'CRM',
            'description' => 'Categoria CRM',
        ]);

        $productOne = $this->createProduct(
            categoryId: $category->id,
            name: 'CRM Core',
            unitPrice: 100,
            status: StateProductEnum::AVAILABLE,
        );

        $productTwo = $this->createProduct(
            categoryId: $category->id,
            name: 'CRM Pro',
            unitPrice: 50,
            status: StateProductEnum::AVAILABLE,
        );

        $component = Livewire::test(AddOrder::class)
            ->set('clientId', (string) $clientId)
            ->set('date', '2026-03-29')
            ->set('observations', 'Orden de prueba con multiples items')
            ->set('items.0.product_id', (string) $productOne->id)
            ->set('items.0.count', 2)
            ->call('addItem')
            ->set('items.1.product_id', (string) $productTwo->id)
            ->set('items.1.count', 3)
            ->call('saveOrder')
            ->assertHasNoErrors();

        $order = Order::query()->latest('id')->first();

        $this->assertNotNull($order);

        $component->assertRedirect(route('orders.show', $order, absolute: false));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'client_id' => $clientId,
            'state' => StateOrderEnum::PENDING->value,
            'total' => 350,
            'observations' => 'Orden de prueba con multiples items',
        ]);

        $this->assertSame('2026-03-29', $order->date?->format('Y-m-d'));

        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'product_id' => $productOne->id,
            'count' => 2,
            'unit_price' => 100,
            'subtotal' => 200,
        ]);

        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'product_id' => $productTwo->id,
            'count' => 3,
            'unit_price' => 50,
            'subtotal' => 150,
        ]);

        $this->assertTrue(session()->has('message'));
    }

    public function test_it_rejects_store_when_item_product_is_out_of_stock(): void
    {
        $clientId = $this->createClient('Ana', 'Gomez', 'ana.order.store@example.com');

        $category = Category::query()->create([
            'name' => 'ERP',
            'description' => 'Categoria ERP',
        ]);

        $outOfStockProduct = $this->createProduct(
            categoryId: $category->id,
            name: 'ERP Sin Stock',
            unitPrice: 75,
            status: StateProductEnum::OUT_OF_STOCK,
        );

        Livewire::test(AddOrder::class)
            ->set('clientId', (string) $clientId)
            ->set('date', '2026-03-29')
            ->set('items.0.product_id', (string) $outOfStockProduct->id)
            ->set('items.0.count', 1)
            ->call('saveOrder')
            ->assertHasErrors(['items.0.product_id'])
            ->assertSee('No se puede agregar un producto sin stock.')
            ->assertNoRedirect();

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_details', 0);
    }

    public function test_it_rejects_store_when_item_count_is_invalid(): void
    {
        $clientId = $this->createClient('Carlos', 'Lopez', 'carlos.order.store@example.com');

        $category = Category::query()->create([
            'name' => 'Marketing',
            'description' => 'Categoria Marketing',
        ]);

        $product = $this->createProduct(
            categoryId: $category->id,
            name: 'Automation Base',
            unitPrice: 120,
            status: StateProductEnum::AVAILABLE,
        );

        Livewire::test(AddOrder::class)
            ->set('clientId', (string) $clientId)
            ->set('date', '2026-03-29')
            ->set('items.0.product_id', (string) $product->id)
            ->set('items.0.count', 0)
            ->call('saveOrder')
            ->assertHasErrors(['items.0.count' => 'min'])
            ->assertNoRedirect();

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_details', 0);
    }

    private function createClient(string $firstname, string $lastname, string $email): int
    {
        return \App\Models\Client::query()->insertGetId([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => '123456789',
            'address' => 'Calle 123',
            'company' => 'GP2',
            'state' => StateEnum::ACTIVE->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }

    private function createProduct(
        int $categoryId,
        string $name,
        float $unitPrice,
        StateProductEnum $status,
    ): \App\Models\Product {
        return \App\Models\Product::query()->create([
            'category_id' => $categoryId,
            'name' => $name,
            'description' => 'Producto para tests de ordenes',
            'unit_price' => $unitPrice,
            'status' => $status->value,
        ]);
    }
}
