<?php

namespace Tests\Unit\Clients;

use App\Enums\StateEnum;
use App\Livewire\Actions\Clients\DeactivateClient;
use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeactivateClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sets_client_state_to_inactive(): void
    {
        $client = Client::query()->create([
            'firstname' => 'Mario',
            'lastname' => 'Perez',
            'email' => 'mario.perez@example.com',
            'state' => StateEnum::ACTIVE,
        ]);

        $action = new DeactivateClient();

        $status = $action($client->id);

        $this->assertTrue($status);
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'state' => StateEnum::INACTIVE->value,
        ]);
    }

    public function test_it_throws_when_client_does_not_exist(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $action = new DeactivateClient();
        $action(9999);
    }
}
