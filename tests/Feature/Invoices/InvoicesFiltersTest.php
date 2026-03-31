<?php

namespace Tests\Feature\Invoices;

use App\Enums\StateEnum;
use App\Enums\StateInvoiceEnum;
use App\Enums\StateOrderEnum;
use App\Livewire\Invoices\IndexInvoices;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class InvoicesFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_invoices_by_client_name(): void
    {
        $juanClientId = $this->createClient('Juan', 'Perez', 'juan-invoice@example.com');
        $anaClientId = $this->createClient('Ana', 'Gomez', 'ana-invoice@example.com');

        $juanOrderId = $this->createOrder($juanClientId, '2026-03-10');
        $anaOrderId = $this->createOrder($anaClientId, '2026-03-10');

        $this->createInvoice($juanOrderId, 'FAC-2026-1001', StateInvoiceEnum::ISSUED, '2026-03-10', 100);
        $this->createInvoice($anaOrderId, 'FAC-2026-1002', StateInvoiceEnum::ISSUED, '2026-03-10', 100);

        Livewire::test(IndexInvoices::class)
            ->set('search', 'juan')
            ->assertSee('Juan Perez')
            ->assertDontSee('Ana Gomez');
    }

    public function test_it_filters_invoices_by_invoice_number(): void
    {
        $firstClientId = $this->createClient('Numero', 'Uno', 'numero-uno@example.com');
        $secondClientId = $this->createClient('Numero', 'Dos', 'numero-dos@example.com');

        $firstOrderId = $this->createOrder($firstClientId, '2026-03-11');
        $secondOrderId = $this->createOrder($secondClientId, '2026-03-11');

        $this->createInvoice($firstOrderId, 'FAC-2026-2001', StateInvoiceEnum::ISSUED, '2026-03-11', 120);
        $this->createInvoice($secondOrderId, 'FAC-2026-2002', StateInvoiceEnum::ISSUED, '2026-03-11', 150);

        Livewire::test(IndexInvoices::class)
            ->set('search', '2002')
            ->assertSee('Numero Dos')
            ->assertDontSee('Numero Uno');
    }

    public function test_it_filters_invoices_by_state(): void
    {
        $issuedClientId = $this->createClient('Estado', 'Emitida', 'estado-emitida@example.com');
        $paidClientId = $this->createClient('Estado', 'Pagada', 'estado-pagada@example.com');

        $issuedOrderId = $this->createOrder($issuedClientId, '2026-03-12');
        $paidOrderId = $this->createOrder($paidClientId, '2026-03-12');

        $this->createInvoice($issuedOrderId, 'FAC-2026-3001', StateInvoiceEnum::ISSUED, '2026-03-12', 100);
        $this->createInvoice($paidOrderId, 'FAC-2026-3002', StateInvoiceEnum::PAID, '2026-03-12', 200);

        Livewire::test(IndexInvoices::class)
            ->set('stateFilter', StateInvoiceEnum::PAID->value)
            ->assertSee('Estado Pagada')
            ->assertDontSee('Estado Emitida');
    }

    public function test_it_filters_invoices_by_date_range(): void
    {
        $outFromClientId = $this->createClient('Fecha', 'FueraInicio', 'fecha-invoice-fuera-inicio@example.com');
        $inRangeClientId = $this->createClient('Fecha', 'EnRango', 'fecha-invoice-en-rango@example.com');
        $outToClientId = $this->createClient('Fecha', 'FueraFin', 'fecha-invoice-fuera-fin@example.com');

        $outFromOrderId = $this->createOrder($outFromClientId, '2026-03-01');
        $inRangeOrderId = $this->createOrder($inRangeClientId, '2026-03-20');
        $outToOrderId = $this->createOrder($outToClientId, '2026-04-10');

        $this->createInvoice($outFromOrderId, 'FAC-2026-4001', StateInvoiceEnum::ISSUED, '2026-03-01', 100);
        $this->createInvoice($inRangeOrderId, 'FAC-2026-4002', StateInvoiceEnum::ISSUED, '2026-03-20', 100);
        $this->createInvoice($outToOrderId, 'FAC-2026-4003', StateInvoiceEnum::ISSUED, '2026-04-10', 100);

        Livewire::test(IndexInvoices::class)
            ->set('fromDate', '2026-03-10')
            ->set('toDate', '2026-03-31')
            ->assertSee('Fecha EnRango')
            ->assertDontSee('Fecha FueraInicio')
            ->assertDontSee('Fecha FueraFin');
    }

    public function test_it_combines_search_state_and_date_filters(): void
    {
        $targetClientId = $this->createClient('Carlos', 'Pagada', 'carlos-pagada@example.com');
        $issuedClientId = $this->createClient('Carlos', 'Emitida', 'carlos-emitida@example.com');
        $outDateClientId = $this->createClient('Carlos', 'FueraFecha', 'carlos-fuera-fecha-invoice@example.com');

        $targetOrderId = $this->createOrder($targetClientId, '2026-03-25');
        $issuedOrderId = $this->createOrder($issuedClientId, '2026-03-25');
        $outDateOrderId = $this->createOrder($outDateClientId, '2026-04-15');

        $this->createInvoice($targetOrderId, 'FAC-2026-5001', StateInvoiceEnum::PAID, '2026-03-25', 400);
        $this->createInvoice($issuedOrderId, 'FAC-2026-5002', StateInvoiceEnum::ISSUED, '2026-03-25', 200);
        $this->createInvoice($outDateOrderId, 'FAC-2026-5003', StateInvoiceEnum::PAID, '2026-04-15', 300);

        Livewire::test(IndexInvoices::class)
            ->set('search', 'carlos')
            ->set('stateFilter', StateInvoiceEnum::PAID->value)
            ->set('fromDate', '2026-03-01')
            ->set('toDate', '2026-03-31')
            ->assertSee('Carlos Pagada')
            ->assertDontSee('Carlos Emitida')
            ->assertDontSee('Carlos FueraFecha');
    }

    public function test_it_resets_pagination_when_filters_change(): void
    {
        for ($index = 1; $index <= 11; $index++) {
            $number = str_pad((string) $index, 2, '0', STR_PAD_LEFT);
            $clientId = $this->createClient(
                "Cliente {$number}",
                "Factura {$number}",
                "cliente-factura-{$number}@example.com",
            );
            $orderId = $this->createOrder($clientId, '2026-03-15');

            $this->createInvoice(
                $orderId,
                "FAC-2026-600{$number}",
                StateInvoiceEnum::ISSUED,
                '2026-03-15',
                100 + $index,
            );
        }

        $paidClientId = $this->createClient('Filtro', 'Estado', 'filtro-estado-invoice@example.com');
        $paidOrderId = $this->createOrder($paidClientId, '2026-03-20');

        $this->createInvoice(
            $paidOrderId,
            'FAC-2026-6999',
            StateInvoiceEnum::PAID,
            '2026-03-20',
            999,
        );

        Livewire::test(IndexInvoices::class)
            ->call('gotoPage', 2)
            ->assertSee('Cliente 01 Factura 01')
            ->set('stateFilter', StateInvoiceEnum::PAID->value)
            ->assertSee('Filtro Estado')
            ->assertDontSee('Cliente 01 Factura 01');
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
