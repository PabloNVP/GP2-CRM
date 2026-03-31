<?php

namespace Tests\Feature\Invoices;

use App\Enums\StateEnum;
use App\Enums\StateInvoiceEnum;
use App\Enums\StateOrderEnum;
use App\Livewire\Invoices\ShowInvoice;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceStateTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_marks_issued_invoice_as_paid(): void
    {
        $clientId = $this->createClient('Pago', 'Valido', 'pago.valido@example.com');
        $orderId = $this->createOrder($clientId, '2026-03-29');

        $invoiceId = $this->createInvoice(
            orderId: $orderId,
            number: 'FAC-PAGO-0001',
            state: StateInvoiceEnum::ISSUED,
            issueDate: '2026-03-29',
            totalAmount: 100,
        );

        $invoice = Invoice::query()->findOrFail($invoiceId);

        Livewire::test(ShowInvoice::class, ['invoice' => $invoice])
            ->call('markAsPaid')
            ->assertSee('Factura marcada como pagada correctamente.');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoiceId,
            'state' => StateInvoiceEnum::PAID->value,
        ]);
    }

    public function test_it_opens_void_modal_and_voids_issued_invoice(): void
    {
        $clientId = $this->createClient('Anular', 'Valida', 'anular.valida@example.com');
        $orderId = $this->createOrder($clientId, '2026-03-29');

        $invoiceId = $this->createInvoice(
            orderId: $orderId,
            number: 'FAC-VOID-0001',
            state: StateInvoiceEnum::ISSUED,
            issueDate: '2026-03-29',
            totalAmount: 150,
        );

        $invoice = Invoice::query()->findOrFail($invoiceId);

        Livewire::test(ShowInvoice::class, ['invoice' => $invoice])
            ->call('openVoidModal')
            ->assertSet('isVoidModalVisible', true)
            ->call('confirmVoid')
            ->assertSet('isVoidModalVisible', false)
            ->assertSee('Factura anulada correctamente.');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoiceId,
            'state' => StateInvoiceEnum::VOIDED->value,
        ]);
    }

    public function test_it_rejects_paying_voided_invoice(): void
    {
        $clientId = $this->createClient('Pago', 'Anulada', 'pago.anulada@example.com');
        $orderId = $this->createOrder($clientId, '2026-03-29');

        $invoiceId = $this->createInvoice(
            orderId: $orderId,
            number: 'FAC-PAGO-VOID-0001',
            state: StateInvoiceEnum::VOIDED,
            issueDate: '2026-03-29',
            totalAmount: 200,
        );

        $invoice = Invoice::query()->findOrFail($invoiceId);

        Livewire::test(ShowInvoice::class, ['invoice' => $invoice])
            ->call('markAsPaid')
            ->assertSee('No se puede marcar como pagada una factura anulada.');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoiceId,
            'state' => StateInvoiceEnum::VOIDED->value,
        ]);
    }

    public function test_it_rejects_voiding_paid_invoice(): void
    {
        $clientId = $this->createClient('Anular', 'Pagada', 'anular.pagada@example.com');
        $orderId = $this->createOrder($clientId, '2026-03-29');

        $invoiceId = $this->createInvoice(
            orderId: $orderId,
            number: 'FAC-VOID-PAID-0001',
            state: StateInvoiceEnum::PAID,
            issueDate: '2026-03-29',
            totalAmount: 300,
        );

        $invoice = Invoice::query()->findOrFail($invoiceId);

        Livewire::test(ShowInvoice::class, ['invoice' => $invoice])
            ->assertSet('isVoidModalVisible', false)
            ->call('openVoidModal')
            ->assertSet('isVoidModalVisible', false)
            ->assertSee('No se puede anular una factura ya pagada.');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoiceId,
            'state' => StateInvoiceEnum::PAID->value,
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

    private function createOrder(int $clientId, string $date): int
    {
        return DB::table('orders')->insertGetId([
            'client_id' => $clientId,
            'date' => $date,
            'state' => StateOrderEnum::DELIVERED->value,
            'total' => 100,
            'observations' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }

    private function createInvoice(
        int $orderId,
        string $number,
        StateInvoiceEnum $state,
        string $issueDate,
        float $totalAmount,
    ): int {
        return DB::table('invoices')->insertGetId([
            'order_id' => $orderId,
            'number' => $number,
            'issue_date' => $issueDate,
            'total_amount' => $totalAmount,
            'state' => $state->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
