<?php

namespace Tests\Feature\Orders;

use App\Enums\StateEnum;
use App\Enums\StateOrderEnum;
use App\Livewire\Orders\IndexOrders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class OrdersFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_orders_by_client_name(): void
    {
        $juanId = $this->createClient('Juan', 'Perez', 'juan-filters@example.com');
        $anaId = $this->createClient('Ana', 'Gomez', 'ana-filters@example.com');

        $this->createOrder($juanId, StateOrderEnum::PENDING, '2026-03-10', 100);
        $this->createOrder($anaId, StateOrderEnum::PENDING, '2026-03-10', 100);

        Livewire::test(IndexOrders::class)
            ->set('search', 'juan')
            ->assertSee('Juan Perez')
            ->assertDontSee('Ana Gomez');
    }

    public function test_it_filters_orders_by_order_number(): void
    {
        $firstClientId = $this->createClient('Pedido', 'Uno', 'pedido-uno@example.com');
        $secondClientId = $this->createClient('Pedido', 'Dos', 'pedido-dos@example.com');

        $this->createOrder($firstClientId, StateOrderEnum::PENDING, '2026-03-11', 120);
        $secondOrderId = $this->createOrder($secondClientId, StateOrderEnum::PENDING, '2026-03-11', 150);

        Livewire::test(IndexOrders::class)
            ->set('search', (string) $secondOrderId)
            ->assertSee('Pedido Dos')
            ->assertDontSee('Pedido Uno');
    }

    public function test_it_filters_orders_by_state(): void
    {
        $pendingClientId = $this->createClient('Estado', 'Pendiente', 'estado-pendiente@example.com');
        $deliveredClientId = $this->createClient('Estado', 'Entregado', 'estado-entregado@example.com');

        $this->createOrder($pendingClientId, StateOrderEnum::PENDING, '2026-03-12', 100);
        $this->createOrder($deliveredClientId, StateOrderEnum::DELIVERED, '2026-03-12', 200);

        Livewire::test(IndexOrders::class)
            ->set('stateFilter', StateOrderEnum::DELIVERED->value)
            ->assertSee('Estado Entregado')
            ->assertDontSee('Estado Pendiente');
    }

    public function test_it_filters_orders_by_date_range(): void
    {
        $outFromClientId = $this->createClient('Fecha', 'FueraInicio', 'fecha-fuera-inicio@example.com');
        $inRangeClientId = $this->createClient('Fecha', 'EnRango', 'fecha-en-rango@example.com');
        $outToClientId = $this->createClient('Fecha', 'FueraFin', 'fecha-fuera-fin@example.com');

        $this->createOrder($outFromClientId, StateOrderEnum::PENDING, '2026-03-01', 100);
        $this->createOrder($inRangeClientId, StateOrderEnum::PENDING, '2026-03-20', 100);
        $this->createOrder($outToClientId, StateOrderEnum::PENDING, '2026-04-10', 100);

        Livewire::test(IndexOrders::class)
            ->set('fromDate', '2026-03-10')
            ->set('toDate', '2026-03-31')
            ->assertSee('Fecha EnRango')
            ->assertDontSee('Fecha FueraInicio')
            ->assertDontSee('Fecha FueraFin');
    }

    public function test_it_combines_search_state_and_date_filters(): void
    {
        $targetClientId = $this->createClient('Carlos', 'Entregado', 'carlos-entregado@example.com');
        $pendingClientId = $this->createClient('Carlos', 'Pendiente', 'carlos-pendiente@example.com');
        $outDateClientId = $this->createClient('Carlos', 'FueraFecha', 'carlos-fuera-fecha@example.com');

        $this->createOrder($targetClientId, StateOrderEnum::DELIVERED, '2026-03-25', 400);
        $this->createOrder($pendingClientId, StateOrderEnum::PENDING, '2026-03-25', 200);
        $this->createOrder($outDateClientId, StateOrderEnum::DELIVERED, '2026-04-15', 300);

        Livewire::test(IndexOrders::class)
            ->set('search', 'carlos')
            ->set('stateFilter', StateOrderEnum::DELIVERED->value)
            ->set('fromDate', '2026-03-01')
            ->set('toDate', '2026-03-31')
            ->assertSee('Carlos Entregado')
            ->assertDontSee('Carlos Pendiente')
            ->assertDontSee('Carlos FueraFecha');
    }

    public function test_it_resets_pagination_when_filters_change(): void
    {
        for ($index = 1; $index <= 11; $index++) {
            $number = str_pad((string) $index, 2, '0', STR_PAD_LEFT);
            $clientId = $this->createClient(
                "Cliente {$number}",
                "Pedido {$number}",
                "cliente-pedido-{$number}@example.com",
            );

            $this->createOrder($clientId, StateOrderEnum::PENDING, '2026-03-15', 100 + $index);
        }

        $deliveredClientId = $this->createClient('Filtro', 'Estado', 'filtro-estado@example.com');
        $this->createOrder($deliveredClientId, StateOrderEnum::DELIVERED, '2026-03-20', 999);

        Livewire::test(IndexOrders::class)
            ->call('gotoPage', 2)
            ->assertSee('Cliente 01 Pedido 01')
            ->set('stateFilter', StateOrderEnum::DELIVERED->value)
            ->assertSee('Filtro Estado')
            ->assertDontSee('Cliente 01 Pedido 01');
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

    private function createOrder(
        int $clientId,
        StateOrderEnum $state,
        string $date,
        float $total,
    ): int {
        return DB::table('orders')->insertGetId([
            'client_id' => $clientId,
            'date' => $date,
            'state' => $state->value,
            'total' => $total,
            'observations' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }
}
