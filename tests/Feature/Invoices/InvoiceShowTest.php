<?php

namespace Tests\Feature\Invoices;

use App\Enums\StateEnum;
use App\Enums\StateInvoiceEnum;
use App\Enums\StateOrderEnum;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_invoice_detail_with_client_order_and_total(): void
    {
        $user = User::factory()->create([
            'state' => StateEnum::ACTIVE->value,
        ]);

        $clientId = \App\Models\Client::query()->insertGetId([
            'firstname' => 'Lucia',
            'lastname' => 'Suarez',
            'email' => 'lucia.invoice.show@example.com',
            'phone' => '11223344',
            'address' => 'Calle 123',
            'company' => 'Empresa Test',
            'state' => StateEnum::ACTIVE->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        $order = Order::query()->create([
            'client_id' => $clientId,
            'date' => '2026-03-29',
            'state' => StateOrderEnum::DELIVERED->value,
            'total' => 250,
            'observations' => 'Orden asociada a factura',
        ]);

        $invoice = Invoice::query()->create([
            'order_id' => $order->id,
            'number' => 'FAC-20260329-0001',
            'issue_date' => '2026-03-29',
            'total_amount' => 250,
            'state' => StateInvoiceEnum::ISSUED->value,
        ]);

        $this->actingAs($user)
            ->get(route('invoices.show', $invoice))
            ->assertOk()
            ->assertSee('Detalle de Factura FAC-20260329-0001')
            ->assertSee('FAC-20260329-0001')
            ->assertSee('Lucia Suarez')
            ->assertSee('lucia.invoice.show@example.com')
            ->assertSee('11223344')
            ->assertSee('Empresa Test')
            ->assertSee('29/03/2026')
            ->assertSee(StateInvoiceEnum::ISSUED->value)
            ->assertSee((string) $order->id)
            ->assertSee('250,00');
    }

    public function test_it_returns_404_when_invoice_does_not_exist(): void
    {
        $user = User::factory()->create([
            'state' => StateEnum::ACTIVE->value,
        ]);

        $this->actingAs($user)
            ->get('/invoices/999999')
            ->assertNotFound();
    }
}
