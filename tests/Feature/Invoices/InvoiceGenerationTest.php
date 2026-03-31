<?php

namespace Tests\Feature\Invoices;

use App\Enums\StateEnum;
use App\Enums\StateInvoiceEnum;
use App\Enums\StateOrderEnum;
use App\Livewire\Orders\IndexOrders;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_invoice_for_delivered_order(): void
    {
        $clientId = $this->createClient('Factura', 'Valida', 'factura.valida@example.com');

        $order = Order::query()->create([
            'client_id' => $clientId,
            'date' => '2026-03-29',
            'state' => StateOrderEnum::DELIVERED->value,
            'total' => 450,
            'observations' => null,
        ]);

        Livewire::test(IndexOrders::class)
            ->call('generateInvoice', $order->id)
            ->assertSee('emitida correctamente.');

        $invoice = Invoice::query()->where('order_id', $order->id)->first();

        $this->assertNotNull($invoice);
        $this->assertStringStartsWith('FAC-', $invoice->number);
        $this->assertSame(now()->toDateString(), $invoice->issue_date?->format('Y-m-d'));

        $this->assertDatabaseHas('invoices', [
            'order_id' => $order->id,
            'total_amount' => 450,
            'state' => StateInvoiceEnum::ISSUED->value,
        ]);
    }

    public function test_it_rejects_invoice_generation_when_order_is_not_delivered(): void
    {
        $clientId = $this->createClient('Factura', 'Invalida', 'factura.invalida@example.com');

        $order = Order::query()->create([
            'client_id' => $clientId,
            'date' => '2026-03-29',
            'state' => StateOrderEnum::PROCESSING->value,
            'total' => 300,
            'observations' => null,
        ]);

        Livewire::test(IndexOrders::class)
            ->call('generateInvoice', $order->id)
            ->assertSee('Solo se puede emitir factura para una orden en estado Entregado.');

        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_it_rejects_duplicate_invoice_for_same_order(): void
    {
        $clientId = $this->createClient('Factura', 'Duplicada', 'factura.duplicada@example.com');

        $order = Order::query()->create([
            'client_id' => $clientId,
            'date' => '2026-03-29',
            'state' => StateOrderEnum::DELIVERED->value,
            'total' => 700,
            'observations' => null,
        ]);

        Invoice::query()->create([
            'order_id' => $order->id,
            'number' => 'FAC-EXISTENTE-0001',
            'issue_date' => now()->toDateString(),
            'total_amount' => 700,
            'state' => StateInvoiceEnum::ISSUED->value,
        ]);

        Livewire::test(IndexOrders::class)
            ->call('generateInvoice', $order->id)
            ->assertSee('La orden seleccionada ya tiene una factura emitida.');

        $this->assertDatabaseCount('invoices', 1);
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
