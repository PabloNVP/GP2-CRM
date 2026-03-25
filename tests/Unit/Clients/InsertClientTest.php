<?php

namespace Tests\Unit\Clients;

use App\Enums\StateEnum;
use App\Livewire\Actions\Clients\InsertClient;
use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InsertClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_client_when_id_is_null(): void
    {
        $action = new InsertClient();

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
}
