<?php

namespace Tests\Browser;

use App\Enums\StateEnum;
use App\Enums\StateInvoiceEnum;
use App\Enums\StateOrderEnum;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InvoiceFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_it_executes_invoice_flow_from_delivered_order_to_paid(): void
    {
        $user = User::factory()->create();

        $client = Client::query()->create([
            'firstname' => 'Factura',
            'lastname' => 'Dusk',
            'email' => 'factura.dusk.'.uniqid().'@example.com',
            'phone' => '123456789',
            'address' => 'Calle Dusk',
            'company' => 'Empresa Dusk',
            'state' => StateEnum::ACTIVE->value,
        ]);

        $order = Order::query()->create([
            'client_id' => $client->id,
            'date' => '2026-03-31',
            'state' => StateOrderEnum::DELIVERED->value,
            'total' => 320,
            'observations' => 'Orden para flujo Dusk de facturacion',
        ]);

        $this->browse(function (Browser $browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/orders')
                ->assertSee('Ordenes')
                ->press('Emitir factura')
                ->waitForText('emitida correctamente.')
                ->assertSee('Ver factura')
                ->clickLink('Ver factura')
                ->waitForText('Detalle de Factura')
                ->assertSee('Emitida')
                ->press('Registrar pago')
                ->waitForText('Factura marcada como pagada correctamente.')
                ->assertSee('Pagada')
                ->assertDontSee('Registrar pago');
        });

        $invoice = Invoice::query()->where('order_id', $order->id)->first();

        $this->assertNotNull($invoice);
        $this->assertSame(StateInvoiceEnum::PAID, $invoice->state);
    }
}
