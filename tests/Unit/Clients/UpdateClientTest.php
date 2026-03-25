<?php

namespace Tests\Unit\Clients;

use App\Enums\StateEnum;
use App\Livewire\Actions\Clients\UpdateClient;
use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_existing_client(): void
    {
        $client = Client::query()->create([
            'firstname' => 'Laura',
            'lastname' => 'Diaz',
            'email' => 'laura@example.com',
            'state' => StateEnum::ACTIVE,
        ]);

        $action = new UpdateClient();

        $status = $action([
            'firstname' => 'Laura Maria',
            'lastname' => 'Diaz',
            'email' => 'laura@example.com',
            'phone' => null,
            'address' => null,
            'company' => null,
        ], $client->id);

        $this->assertTrue($status);
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'firstname' => 'Laura Maria',
        ]);
    }

    public function test_it_throws_when_updating_non_existing_client(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $action = new UpdateClient();

        $action([
            'firstname' => 'A',
            'lastname' => 'B',
            'email' => 'a.b@example.com',
        ], 9999);
    }
}
