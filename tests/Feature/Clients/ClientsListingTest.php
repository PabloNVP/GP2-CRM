<?php

namespace Tests\Feature\Clients;

use App\Enums\StateEnum;
use App\Livewire\Clients\Index;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ClientsListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_empty_state_when_there_are_no_clients(): void
    {
        Livewire::test(Index::class)
            ->assertSee('No hay clientes registrados');
    }

    public function test_it_paginates_clients_by_ten_records(): void
    {
        $this->seedClients(11);

        Livewire::test(Index::class)
            ->assertSee('Nombre 11 Apellido 11')
            ->assertDontSee('Nombre 01 Apellido 01')
            ->call('gotoPage', 2)
            ->assertSee('Nombre 01 Apellido 01')
            ->assertDontSee('Nombre 11 Apellido 11');
    }

    private function seedClients(int $count): void
    {
        $rows = [];
        $timestamp = now();

        for ($index = 1; $index <= $count; $index++) {
            $number = str_pad((string) $index, 2, '0', STR_PAD_LEFT);

            $rows[] = [
                'firstname' => "Nombre {$number}",
                'lastname' => "Apellido {$number}",
                'email' => "cliente{$number}@example.com",
                'phone' => "123456{$number}",
                'address' => "Calle {$number}",
                'company' => "Empresa {$number}",
                'state' => StateEnum::ACTIVE->value,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'deleted_at' => null,
            ];
        }

        DB::table('clients')->insert($rows);
    }
}
