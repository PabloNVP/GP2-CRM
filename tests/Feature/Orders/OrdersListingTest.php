<?php

namespace Tests\Feature\Orders;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Enums\StateOrderEnum;
use App\Livewire\Orders\IndexOrders;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class OrdersListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_empty_state_when_there_are_no_orders(): void
    {
        Livewire::test(IndexOrders::class)
            ->assertSee('No hay ordenes registradas');
    }

    public function test_it_displays_required_columns_on_the_listing(): void
    {
        $clientId = DB::table('clients')->insertGetId([
            'firstname' => 'Nico',
            'lastname' => 'Martinez',
            'email' => 'nico.orders@example.com',
            'phone' => '123456789',
            'address' => 'Calle Principal 123',
            'company' => 'GP2',
            'state' => StateEnum::ACTIVE->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        DB::table('orders')->insert([
            'client_id' => $clientId,
            'date' => '2026-03-29',
            'state' => StateOrderEnum::DELIVERED->value,
            'total' => 1234.50,
            'observations' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        Livewire::test(IndexOrders::class)
            ->assertSee('Numero de orden')
            ->assertSee('Cliente')
            ->assertSee('Fecha')
            ->assertSee('Estado')
            ->assertSee('Total')
            ->assertSee('Nico Martinez')
            ->assertSee('29/03/2026')
            ->assertSee(StateOrderEnum::DELIVERED->value)
            ->assertSee('1.234,50');
    }

    public function test_it_paginates_orders_by_ten_records(): void
    {
        $this->seedOrders(11);

        Livewire::test(IndexOrders::class)
            ->assertSee('Cliente 11 Orden 11')
            ->assertDontSee('Cliente 01 Orden 01')
            ->call('gotoPage', 2)
            ->assertSee('Cliente 01 Orden 01')
            ->assertDontSee('Cliente 11 Orden 11');
    }

    public function test_orders_route_is_available_for_authenticated_active_users(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $this->actingAs($user)
            ->get('/orders')
            ->assertOk()
            ->assertSee('Ordenes');
    }

    public function test_orders_route_redirects_guests_to_login(): void
    {
        $this->get('/orders')
            ->assertRedirect('/login');
    }

    private function seedOrders(int $count): void
    {
        $orderRows = [];

        for ($index = 1; $index <= $count; $index++) {
            $number = str_pad((string) $index, 2, '0', STR_PAD_LEFT);

            $clientId = DB::table('clients')->insertGetId([
                'firstname' => "Cliente {$number}",
                'lastname' => "Orden {$number}",
                'email' => "cliente.order.{$number}@example.com",
                'phone' => "12345{$number}",
                'address' => "Calle {$number}",
                'company' => "Empresa {$number}",
                'state' => StateEnum::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);

            $orderRows[] = [
                'client_id' => $clientId,
                'date' => now()->toDateString(),
                'state' => StateOrderEnum::PENDING->value,
                'total' => $index * 100,
                'observations' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ];
        }

        DB::table('orders')->insert($orderRows);
    }
}
