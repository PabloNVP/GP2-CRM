<?php

namespace Tests\Unit\Clients;

use App\Enums\StateEnum;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_state_is_cast_to_enum(): void
    {
        $client = Client::query()->create([
            'firstname' => 'Juan',
            'lastname' => 'Perez',
            'email' => 'juan.cast@example.com',
            'state' => StateEnum::ACTIVE,
        ]);

        $this->assertInstanceOf(StateEnum::class, $client->state);
        $this->assertSame(StateEnum::ACTIVE, $client->state);
    }

    public function test_fillable_does_not_include_timestamp_or_soft_delete_columns(): void
    {
        $fillable = (new Client())->getFillable();

        $this->assertNotContains('created_at', $fillable);
        $this->assertNotContains('updated_at', $fillable);
        $this->assertNotContains('deleted_at', $fillable);
    }
}
