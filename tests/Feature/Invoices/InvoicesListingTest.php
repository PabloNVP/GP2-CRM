<?php

namespace Tests\Feature\Invoices;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Enums\StateInvoiceEnum;
use App\Enums\StateOrderEnum;
use App\Livewire\Invoices\IndexInvoices;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class InvoicesListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_empty_state_when_there_are_no_invoices(): void
    {
        Livewire::test(IndexInvoices::class)
            ->assertSee('No hay facturas registradas');
    }

    public function test_it_displays_required_columns_on_the_listing(): void
    {
        $clientId = $this->createClient('Nico', 'Martinez', 'nico.invoices@example.com');
        $orderId = $this->createOrder($clientId, '2026-03-29');

        $this->createInvoice(
            orderId: $orderId,
            number: 'FAC-2026-0001',
            state: StateInvoiceEnum::ISSUED,
            issueDate: '2026-03-29',
            totalAmount: 1234.50,
        );

        Livewire::test(IndexInvoices::class)
            ->assertSee('Numero de factura')
            ->assertSee('Orden')
            ->assertSee('Cliente')
            ->assertSee('Fecha de emision')
            ->assertSee('Estado')
            ->assertSee('Total')
            ->assertSee('FAC-2026-0001')
            ->assertSee('Nico Martinez')
            ->assertSee('29/03/2026')
            ->assertSee(StateInvoiceEnum::ISSUED->value)
            ->assertSee('1.234,50');
    }

    public function test_it_paginates_invoices_by_ten_records(): void
    {
        $this->seedInvoices(11);

        Livewire::test(IndexInvoices::class)
            ->assertSee('Cliente 11 Factura 11')
            ->assertDontSee('Cliente 01 Factura 01')
            ->call('gotoPage', 2)
            ->assertSee('Cliente 01 Factura 01')
            ->assertDontSee('Cliente 11 Factura 11');
    }

    public function test_invoices_route_is_available_for_authenticated_active_users(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $this->actingAs($user)
            ->get('/invoices')
            ->assertOk()
            ->assertSee('Facturas');
    }

    public function test_invoices_route_redirects_guests_to_login(): void
    {
        $this->get('/invoices')
            ->assertRedirect('/login');
    }

    private function seedInvoices(int $count): void
    {
        for ($index = 1; $index <= $count; $index++) {
            $number = str_pad((string) $index, 2, '0', STR_PAD_LEFT);
            $clientId = $this->createClient(
                "Cliente {$number}",
                "Factura {$number}",
                "cliente.invoice.{$number}@example.com",
            );
            $orderId = $this->createOrder($clientId, '2026-03-15');

            $this->createInvoice(
                orderId: $orderId,
                number: "FAC-2026-{$number}",
                state: StateInvoiceEnum::ISSUED,
                issueDate: '2026-03-15',
                totalAmount: $index * 100,
            );
        }
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
