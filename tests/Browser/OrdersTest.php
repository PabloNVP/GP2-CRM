<?php

namespace Tests\Browser;

use App\Enums\StateProductEnum;
use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OrdersTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_it_completes_critical_order_flow_create_and_deliver(): void
    {
        $user = User::factory()->create();

        $client = Client::query()->create([
            'firstname' => 'Cliente',
            'lastname' => 'Dusk',
            'email' => 'cliente.dusk.'.uniqid().'@example.com',
            'phone' => '123456789',
            'address' => 'Calle Dusk',
            'company' => 'Empresa Dusk',
        ]);

        $category = Category::query()->create([
            'name' => 'Categoria Dusk '.uniqid(),
            'description' => 'Categoria para ordenes E2E',
        ]);

        $productName = 'Producto Dusk '.uniqid();

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => $productName,
            'description' => 'Producto para flujo E2E',
            'unit_price' => 120,
            'status' => StateProductEnum::AVAILABLE->value,
        ]);

        $this->browse(function (Browser $browser) use ($user, $client, $product, $productName) {
            $browser->loginAs($user)
                ->visit('/orders')
                ->assertSee('Ordenes')
                ->clickLink('Agregar orden')
                ->waitForText('Agregar Orden')
                ->select('#clientId', (string) $client->id);

            $browser->script("const dateInput = document.querySelector('#date'); dateInput.value = '2026-03-29'; dateInput.dispatchEvent(new Event('input', { bubbles: true })); dateInput.dispatchEvent(new Event('change', { bubbles: true }));");
            $browser->pause(150);

            $browser->select('#itemProduct-0', (string) $product->id);
            $browser->click('#itemCount-0')
                ->keys('#itemCount-0', '{backspace}', '2')
                ->waitForTextIn('#orderTotal', '240,00');

            $browser
                ->click('button[type="submit"]')
                ->waitForText('Detalle de Orden #')
                ->assertSee($productName)
                ->assertSee('240,00');

            $browser->visit('/orders')
                ->press('Pasar a En proceso')
                ->waitForText('Estado de la orden actualizado correctamente.');

            $browser->visit('/orders')
                ->press('Pasar a Enviado')
                ->waitForText('Estado de la orden actualizado correctamente.');

            $browser->visit('/orders')
                ->press('Pasar a Entregado')
                ->waitForText('Estado de la orden actualizado correctamente.');
        });

        $order = Order::query()->latest('id')->first();

        $this->assertNotNull($order);
        $this->assertSame('Entregado', $order->state->value);
        $this->assertSame('240.00', (string) $order->total);
    }
}
