<?php

namespace Tests\Unit\Invoices;

use App\Enums\StateEnum;
use App\Enums\StateInvoiceEnum;
use App\Enums\StateOrderEnum;
use App\Livewire\Actions\Invoices\GenerateInvoice;
use App\Models\Order;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GenerateInvoiceActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_unique_invoice_numbers_with_expected_format(): void
    {
        $clientId = $this->createClient('Unit', 'Generator', 'unit.generator@example.com');

        $orderOne = $this->createDeliveredOrder($clientId, 350);
        $orderTwo = $this->createDeliveredOrder($clientId, 410);

        $action = new GenerateInvoice();

        $invoiceOne = $action($orderOne->id);
        $invoiceTwo = $action($orderTwo->id);

        $this->assertMatchesRegularExpression('/^FAC-\\d{14}-\\d{4}$/', $invoiceOne->number);
        $this->assertMatchesRegularExpression('/^FAC-\\d{14}-\\d{4}$/', $invoiceTwo->number);
        $this->assertNotSame($invoiceOne->number, $invoiceTwo->number);

        $this->assertSame(StateInvoiceEnum::ISSUED, $invoiceOne->state);
        $this->assertSame(StateInvoiceEnum::ISSUED, $invoiceTwo->state);
        $this->assertSame(now()->toDateString(), $invoiceOne->issue_date?->format('Y-m-d'));
        $this->assertSame(now()->toDateString(), $invoiceTwo->issue_date?->format('Y-m-d'));
    }

    public function test_it_rejects_generation_for_non_delivered_order(): void
    {
        $clientId = $this->createClient('Unit', 'Rejected', 'unit.rejected@example.com');

        $order = Order::query()->create([
            'client_id' => $clientId,
            'date' => '2026-03-31',
            'state' => StateOrderEnum::PROCESSING->value,
            'total' => 150,
            'observations' => null,
        ]);

        $action = new GenerateInvoice();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Solo se puede emitir factura para una orden en estado Entregado.');

        $action($order->id);
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

    private function createDeliveredOrder(int $clientId, float $total): Order
    {
        return Order::query()->create([
            'client_id' => $clientId,
            'date' => '2026-03-31',
            'state' => StateOrderEnum::DELIVERED->value,
            'total' => $total,
            'observations' => null,
        ]);
    }
}
