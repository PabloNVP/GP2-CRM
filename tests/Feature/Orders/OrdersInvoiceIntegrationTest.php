<?php

namespace Tests\Feature\Orders;

use App\Enums\StateEnum;
use App\Enums\StateInvoiceEnum;
use App\Enums\StateOrderEnum;
use App\Livewire\Orders\IndexOrders;
use App\Livewire\Orders\ShowOrder;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class OrdersInvoiceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_invoice_from_delivered_order_in_orders_listing_and_shows_link(): void
    {
        $clientId = $this->createClient('Integracion', 'Listado', 'integracion.listado@example.com');

        $order = Order::query()->create([
            'client_id' => $clientId,
            'date' => '2026-03-29',
            'state' => StateOrderEnum::DELIVERED->value,
            'total' => 120,
            'observations' => null,
        ]);

        Livewire::test(IndexOrders::class)
            ->assertSee('Emitir factura')
            ->call('generateInvoice', $order->id)
            ->assertSee('emitida correctamente.')
            ->assertSee('Ver factura')
            ->assertDontSee('Emitir factura');

        $this->assertDatabaseHas('invoices', [
            'order_id' => $order->id,
            'state' => StateInvoiceEnum::ISSUED->value,
        ]);
    }

    public function test_it_shows_invoice_link_and_state_when_order_already_has_invoice(): void
    {
        $clientId = $this->createClient('Integracion', 'Existente', 'integracion.existente@example.com');

        $order = Order::query()->create([
            'client_id' => $clientId,
            'date' => '2026-03-29',
            'state' => StateOrderEnum::DELIVERED->value,
            'total' => 200,
            'observations' => null,
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'number' => 'FAC-INTEG-0001',
            'issue_date' => '2026-03-29',
            'total_amount' => 200,
            'state' => StateInvoiceEnum::PAID->value,
        ]);

        Livewire::test(IndexOrders::class)
            ->assertSee('Factura '.$invoice->number)
            ->assertSee(StateInvoiceEnum::PAID->value)
            ->assertSee('Ver factura')
            ->assertDontSee('Emitir factura');

        Livewire::test(ShowOrder::class, ['order' => $order])
            ->assertSee('Facturacion')
            ->assertSee($invoice->number)
            ->assertSee(StateInvoiceEnum::PAID->value)
            ->assertSee('Ver factura')
            ->assertDontSee('Emitir factura');
    }

    public function test_it_generates_invoice_from_order_detail_when_delivered(): void
    {
        $clientId = $this->createClient('Integracion', 'Detalle', 'integracion.detalle@example.com');

        $order = Order::query()->create([
            'client_id' => $clientId,
            'date' => '2026-03-29',
            'state' => StateOrderEnum::DELIVERED->value,
            'total' => 180,
            'observations' => null,
        ]);

        Livewire::test(ShowOrder::class, ['order' => $order])
            ->assertSee('Emitir factura')
            ->call('generateInvoice')
            ->assertSee('emitida correctamente.')
            ->assertSee('Ver factura')
            ->assertDontSee('Emitir factura');

        $this->assertDatabaseHas('invoices', [
            'order_id' => $order->id,
            'state' => StateInvoiceEnum::ISSUED->value,
        ]);
    }

    private function createClient(string $firstname, string $lastname, string $email): int
    {
        return DB::table('clients')->insertGetId([
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
