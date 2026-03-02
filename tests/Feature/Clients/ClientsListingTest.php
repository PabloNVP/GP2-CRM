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

    public function test_it_filters_clients_by_name_or_email_when_search_has_three_or_more_characters(): void
    {
        DB::table('clients')->insert([
            [
                'firstname' => 'Juan',
                'lastname' => 'Perez',
                'email' => 'juan@example.com',
                'phone' => '123456',
                'address' => 'Calle 1',
                'company' => 'Empresa A',
                'state' => StateEnum::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'firstname' => 'Ana',
                'lastname' => 'Gomez',
                'email' => 'ana@example.com',
                'phone' => '654321',
                'address' => 'Calle 2',
                'company' => 'Empresa B',
                'state' => StateEnum::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ]);

        Livewire::test(Index::class)
            ->set('search', 'juan')
            ->assertSee('Juan Perez')
            ->assertDontSee('Ana Gomez');

        Livewire::test(Index::class)
            ->set('search', 'ana@example.com')
            ->assertSee('Ana Gomez')
            ->assertDontSee('Juan Perez');
    }

    public function test_it_does_not_filter_when_search_has_less_than_three_characters(): void
    {
        DB::table('clients')->insert([
            [
                'firstname' => 'Leo',
                'lastname' => 'Suarez',
                'email' => 'leo@example.com',
                'phone' => '111111',
                'address' => 'Calle 3',
                'company' => 'Empresa C',
                'state' => StateEnum::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'firstname' => 'Lara',
                'lastname' => 'Lopez',
                'email' => 'lara@example.com',
                'phone' => '222222',
                'address' => 'Calle 4',
                'company' => 'Empresa D',
                'state' => StateEnum::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ]);

        Livewire::test(Index::class)
            ->set('search', 'la')
            ->assertSee('Leo Suarez')
            ->assertSee('Lara Lopez');
    }

    public function test_it_filters_clients_by_state(): void
    {
        DB::table('clients')->insert([
            [
                'firstname' => 'Mario',
                'lastname' => 'Activo',
                'email' => 'mario.activo@example.com',
                'phone' => '111111',
                'address' => 'Calle 1',
                'company' => 'Empresa A',
                'state' => StateEnum::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'firstname' => 'Maria',
                'lastname' => 'Inactiva',
                'email' => 'maria.inactiva@example.com',
                'phone' => '222222',
                'address' => 'Calle 2',
                'company' => 'Empresa B',
                'state' => StateEnum::INACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ]);

        Livewire::test(Index::class)
            ->set('stateFilter', StateEnum::ACTIVE->value)
            ->assertSee('Mario Activo')
            ->assertDontSee('Maria Inactiva');

        Livewire::test(Index::class)
            ->set('stateFilter', StateEnum::INACTIVE->value)
            ->assertSee('Maria Inactiva')
            ->assertDontSee('Mario Activo');

        Livewire::test(Index::class)
            ->set('stateFilter', '')
            ->assertSee('Mario Activo')
            ->assertSee('Maria Inactiva');
    }

    public function test_it_combines_search_and_state_filters(): void
    {
        DB::table('clients')->insert([
            [
                'firstname' => 'Carlos',
                'lastname' => 'Activo',
                'email' => 'carlos@example.com',
                'phone' => '333333',
                'address' => 'Calle 3',
                'company' => 'Empresa C',
                'state' => StateEnum::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'firstname' => 'Carlos',
                'lastname' => 'Inactivo',
                'email' => 'carlos.inactivo@example.com',
                'phone' => '444444',
                'address' => 'Calle 4',
                'company' => 'Empresa D',
                'state' => StateEnum::INACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ]);

        Livewire::test(Index::class)
            ->set('search', 'carlos')
            ->set('stateFilter', StateEnum::INACTIVE->value)
            ->assertSee('Carlos Inactivo')
            ->assertDontSee('Carlos Activo');
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
