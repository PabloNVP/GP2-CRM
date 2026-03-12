<?php

namespace Tests\Unit\Clients;

use App\Enums\StateEnum;
use App\Livewire\Actions\Clients\UpsertClient;
use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpsertClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_client_when_id_is_null(): void
    {
        $action = new UpsertClient();

        $status = $action([
            'firstname' => 'Laura',
            'lastname' => 'Diaz',
            'email' => 'laura.diaz@example.com',
            'phone' => '123456',
            'address' => 'Calle 123',
            'company' => 'Acme',
        ]);

        $this->assertTrue($status);
        $this->assertDatabaseHas('clients', [
            'email' => 'laura.diaz@example.com',
            'state' => StateEnum::ACTIVE->value,
        ]);
    }

    public function test_it_updates_existing_client(): void
    {
        $client = Client::query()->create([
            'firstname' => 'Laura',
            'lastname' => 'Diaz',
            'email' => 'laura@example.com',
            'state' => StateEnum::ACTIVE,
        ]);

        $action = new UpsertClient();

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

        $action = new UpsertClient();

        $action([
            'firstname' => 'A',
            'lastname' => 'B',
            'email' => 'a.b@example.com',
        ], 9999);
    }
}
