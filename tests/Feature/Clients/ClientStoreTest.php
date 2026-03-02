<?php

namespace Tests\Feature\Clients;

use App\Enums\StateEnum;
use App\Livewire\Clients\AddClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ClientStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_client_successfully(): void
    {
        Livewire::test(AddClient::class)
            ->set('firstname', 'Juan')
            ->set('lastname', 'Perez')
            ->set('email', 'juan.perez@example.com')
            ->set('phone', '123456789')
            ->set('address', 'Calle Falsa 123')
            ->set('company', 'Acme SA')
            ->call('saveClient')
            ->assertHasNoErrors()
            ->assertRedirect(route('clients.index', absolute: false));

        $this->assertDatabaseHas('clients', [
            'firstname' => 'Juan',
            'lastname' => 'Perez',
            'email' => 'juan.perez@example.com',
            'company' => 'Acme SA',
        ]);

        $this->assertTrue(session()->has('message'));
    }

    public function test_it_rejects_store_when_email_is_duplicated(): void
    {
        DB::table('clients')->insert([
            'firstname' => 'Ana',
            'lastname' => 'Gomez',
            'email' => 'ana@example.com',
            'phone' => '111111111',
            'address' => 'Calle 1',
            'company' => 'Empresa A',
            'state' => StateEnum::ACTIVE->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        Livewire::test(AddClient::class)
            ->set('firstname', 'Ana Maria')
            ->set('lastname', 'Lopez')
            ->set('email', 'ana@example.com')
            ->set('phone', '222222222')
            ->set('address', 'Calle 2')
            ->set('company', 'Empresa B')
            ->call('saveClient')
            ->assertHasErrors(['email' => 'unique'])
            ->assertSee('El correo electrónico ya está registrado.')
            ->assertNoRedirect();

        $this->assertDatabaseCount('clients', 1);
    }
}
