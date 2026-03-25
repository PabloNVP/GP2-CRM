<?php

namespace Tests\Feature\Clients;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Client;
use App\Livewire\Clients\DeleteClient;
use App\Enums\StateEnum;
use Livewire\Livewire;

class ClientsDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deactivates_client_after_upgrade(): void
    {
        $client = Client::create([
            'firstname' => 'Pedro',
            'lastname' => 'Activo',
            'email' => 'pedro.activo@example.com',
            'phone' => '777777',
            'address' => 'Calle 12',
            'company' => 'Empresa Z',
            'state' => StateEnum::ACTIVE->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        Livewire::test(DeleteClient::class, ['client' => $client])
            ->call('confirmAction')
            ->assertDispatched('show-message')
            ->assertDispatched('toggle-visible');

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'state' => StateEnum::INACTIVE->value,
        ]);
    }

    public function test_it_activates_client_after_upgrade(): void
    {
        $client = Client::create([
            'firstname' => 'Pedro',
            'lastname' => 'Activo',
            'email' => 'pedro.activo@example.com',
            'phone' => '777777',
            'address' => 'Calle 12',
            'company' => 'Empresa Z',
            'state' => StateEnum::INACTIVE->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        Livewire::test(DeleteClient::class, ['client' => $client])
            ->call('confirmAction')
            ->assertDispatched('show-message')
            ->assertDispatched('toggle-visible');
           
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'state' => StateEnum::ACTIVE->value,
        ]);
    }
}
