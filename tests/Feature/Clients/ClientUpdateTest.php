<?php

namespace Tests\Feature\Clients;

use App\Enums\StateEnum;
use App\Livewire\Clients\AddClient;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_client_successfully(): void
    {
        $client = Client::query()->create([
            'firstname' => 'Juan',
            'lastname' => 'Perez',
            'email' => 'juan@example.com',
            'phone' => '123456789',
            'address' => 'Calle Falsa 123',
            'company' => 'Acme SA',
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::test(AddClient::class, ['client' => $client])
            ->assertSet('firstname', 'Juan')
            ->assertSet('lastname', 'Perez')
            ->assertSet('email', 'juan@example.com')
            ->set('firstname', 'Juan Carlos')
            ->set('email', 'juan.carlos@example.com')
            ->call('saveClient')
            ->assertHasNoErrors()
            ->assertRedirect(route('clients.index', absolute: false));

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'firstname' => 'Juan Carlos',
            'lastname' => 'Perez',
            'email' => 'juan.carlos@example.com',
        ]);

        $this->assertTrue(session()->has('message'));
    }

    public function test_it_rejects_update_when_email_is_duplicated(): void
    {
        $clientToUpdate = Client::query()->create([
            'firstname' => 'Ana',
            'lastname' => 'Lopez',
            'email' => 'ana@example.com',
            'phone' => '111111111',
            'address' => 'Calle 1',
            'company' => 'Empresa A',
            'state' => StateEnum::ACTIVE->value,
        ]);

        Client::query()->create([
            'firstname' => 'Mario',
            'lastname' => 'Gomez',
            'email' => 'mario@example.com',
            'phone' => '222222222',
            'address' => 'Calle 2',
            'company' => 'Empresa B',
            'state' => StateEnum::ACTIVE->value,
        ]);

        Livewire::test(AddClient::class, ['client' => $clientToUpdate])
            ->set('email', 'mario@example.com')
            ->call('saveClient')
            ->assertHasErrors(['email' => 'unique'])
            ->assertSee('El correo electrónico ya está registrado.')
            ->assertNoRedirect();

        $this->assertDatabaseHas('clients', [
            'id' => $clientToUpdate->id,
            'email' => 'ana@example.com',
        ]);
    }
}
