<?php

namespace Tests\Feature\Orders;

use App\Enums\StateEnum;
use App\Enums\StateOrderEnum;
use App\Enums\StateProductEnum;
use App\Livewire\Orders\AddOrder;
use App\Livewire\Orders\IndexOrders;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_executes_critical_order_flow_from_store_to_delivered(): void
    {
        $user = User::factory()->create([
            'state' => StateEnum::ACTIVE->value,
        ]);

        $clientId = $this->createClient('Flujo', 'Completo', 'flujo.completo@example.com');

        $category = Category::query()->create([
            'name' => 'Flow Category',
            'description' => 'Categoria para flujo',
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Flow Product',
            'description' => 'Producto para flujo',
            'unit_price' => 100,
            'status' => StateProductEnum::AVAILABLE->value,
        ]);

        Livewire::test(AddOrder::class)
            ->set('clientId', (string) $clientId)
            ->set('date', '2026-03-29')
            ->set('items.0.product_id', (string) $product->id)
            ->set('items.0.count', 2)
            ->call('saveOrder')
            ->assertHasNoErrors();

        $order = Order::query()->latest('id')->first();
        $this->assertNotNull($order);

        $this->actingAs($user)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Detalle de Orden #'.$order->id)
            ->assertSee('Flow Product')
            ->assertSee('200,00');

        Livewire::test(IndexOrders::class)
            ->call('changeState', $order->id, StateOrderEnum::PROCESSING->value)
            ->call('changeState', $order->id, StateOrderEnum::SHIPPED->value)
            ->call('changeState', $order->id, StateOrderEnum::DELIVERED->value)
            ->assertSee('Estado de la orden actualizado correctamente.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'state' => StateOrderEnum::DELIVERED->value,
            'total' => 200,
        ]);
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
}
