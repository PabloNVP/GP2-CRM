<?php

namespace Tests\Unit\Invoices;

use App\Enums\StateEnum;
use App\Enums\StateInvoiceEnum;
use App\Enums\StateOrderEnum;
use App\Livewire\Actions\Invoices\MarkInvoiceAsPaid;
use App\Livewire\Actions\Invoices\VoidInvoice;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InvoiceStateRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_allows_transition_from_issued_to_paid(): void
    {
        $invoiceId = $this->createInvoiceWithState(StateInvoiceEnum::ISSUED);

        $action = new MarkInvoiceAsPaid();

        $result = $action($invoiceId);

        $this->assertTrue($result);
        $this->assertDatabaseHas('invoices', [
            'id' => $invoiceId,
            'state' => StateInvoiceEnum::PAID->value,
        ]);
    }

    public function test_it_allows_transition_from_issued_to_voided(): void
    {
        $invoiceId = $this->createInvoiceWithState(StateInvoiceEnum::ISSUED);

        $action = new VoidInvoice();

        $result = $action($invoiceId);

        $this->assertTrue($result);
        $this->assertDatabaseHas('invoices', [
            'id' => $invoiceId,
            'state' => StateInvoiceEnum::VOIDED->value,
        ]);
    }

    public function test_it_rejects_mark_as_paid_when_invoice_is_voided(): void
    {
        $invoiceId = $this->createInvoiceWithState(StateInvoiceEnum::VOIDED);

        $action = new MarkInvoiceAsPaid();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('No se puede marcar como pagada una factura anulada.');

        $action($invoiceId);
    }

    public function test_it_rejects_void_when_invoice_is_paid(): void
    {
        $invoiceId = $this->createInvoiceWithState(StateInvoiceEnum::PAID);

        $action = new VoidInvoice();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('No se puede anular una factura ya pagada.');

        $action($invoiceId);
    }

    private function createInvoiceWithState(StateInvoiceEnum $state): int
    {
        $clientId = DB::table('clients')->insertGetId([
            'firstname' => 'State',
            'lastname' => 'Rules',
            'email' => 'state.rules.'.uniqid().'@example.com',
            'phone' => '123456789',
            'address' => 'Calle 123',
            'company' => 'GP2',
            'state' => StateEnum::ACTIVE->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        $orderId = DB::table('orders')->insertGetId([
            'client_id' => $clientId,
            'date' => '2026-03-31',
            'state' => StateOrderEnum::DELIVERED->value,
            'total' => 100,
            'observations' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        return DB::table('invoices')->insertGetId([
            'order_id' => $orderId,
            'number' => 'FAC-UNIT-'.uniqid(),
            'issue_date' => '2026-03-31',
            'total_amount' => 100,
            'state' => $state->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
