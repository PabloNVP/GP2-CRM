<?php

namespace Tests\Feature\Orders;

use App\Enums\StateEnum;
use App\Enums\StateOrderEnum;
use App\Enums\StateProductEnum;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_order_detail_with_client_items_and_total(): void
    {
        $user = User::factory()->create([
            'state' => StateEnum::ACTIVE->value,
        ]);

        $clientId = \App\Models\Client::query()->insertGetId([
            'firstname' => 'Lucia',
            'lastname' => 'Suarez',
            'email' => 'lucia.show@example.com',
            'phone' => '11223344',
            'address' => 'Calle 123',
            'company' => 'Empresa Test',
            'state' => StateEnum::ACTIVE->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        $category = Category::query()->create([
            'name' => 'CRM',
            'description' => 'Categoria CRM',
        ]);

        $productOne = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'CRM Core',
            'description' => 'Producto uno',
            'unit_price' => 100,
            'status' => StateProductEnum::AVAILABLE->value,
        ]);

        $productTwo = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'CRM Pro',
            'description' => 'Producto dos',
            'unit_price' => 50,
            'status' => StateProductEnum::AVAILABLE->value,
        ]);

        $order = Order::query()->create([
            'client_id' => $clientId,
            'date' => '2026-03-29',
            'state' => StateOrderEnum::PROCESSING->value,
            'total' => 250,
            'observations' => 'Orden para validar detalle',
        ]);

        OrderDetail::query()->create([
            'order_id' => $order->id,
            'product_id' => $productOne->id,
            'count' => 2,
            'unit_price' => 100,
            'subtotal' => 200,
        ]);

        OrderDetail::query()->create([
            'order_id' => $order->id,
            'product_id' => $productTwo->id,
            'count' => 1,
            'unit_price' => 50,
            'subtotal' => 50,
        ]);

        $this->actingAs($user)
            ->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Detalle de Orden #'.$order->id)
            ->assertSee('Lucia Suarez')
            ->assertSee('lucia.show@example.com')
            ->assertSee('11223344')
            ->assertSee('Empresa Test')
            ->assertSee('29/03/2026')
            ->assertSee(StateOrderEnum::PROCESSING->value)
            ->assertSee('Orden para validar detalle')
            ->assertSee('CRM Core')
            ->assertSee('CRM Pro')
            ->assertSee('200,00')
            ->assertSee('50,00')
            ->assertSee('250,00');
    }

    public function test_it_returns_404_when_order_does_not_exist(): void
    {
        $user = User::factory()->create([
            'state' => StateEnum::ACTIVE->value,
        ]);

        $this->actingAs($user)
            ->get('/orders/999999')
            ->assertNotFound();
    }
}
