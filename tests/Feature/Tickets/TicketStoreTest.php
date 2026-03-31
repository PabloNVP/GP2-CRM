<?php

namespace Tests\Feature\Tickets;

use App\Enums\PriorityTicketEnum;
use App\Enums\StateEnum;
use App\Enums\StateProductEnum;
use App\Enums\StateTicketEnum;
use App\Livewire\Tickets\AddTicket;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TicketStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_ticket_successfully_and_redirects_to_detail(): void
    {
        $clientId = $this->createClient('Nico', 'Martinez', 'nico.ticket.store@example.com', StateEnum::ACTIVE);
        $categoryId = $this->createCategory('CRM');
        $productId = $this->createProduct($categoryId, 'CRM Pro', StateProductEnum::AVAILABLE);

        $component = Livewire::test(AddTicket::class)
            ->set('clientId', (string) $clientId)
            ->set('productId', (string) $productId)
            ->set('subject', 'No puedo iniciar sesion')
            ->set('description', 'Al ingresar usuario y clave el sistema devuelve error 500.')
            ->set('priority', PriorityTicketEnum::HIGH->value)
            ->call('saveTicket')
            ->assertHasNoErrors();

        $ticket = Ticket::query()->latest('id')->first();

        $this->assertNotNull($ticket);

        $component->assertRedirect(route('tickets.show', $ticket, absolute: false));

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'client_id' => $clientId,
            'product_id' => $productId,
            'subject' => 'No puedo iniciar sesion',
            'description' => 'Al ingresar usuario y clave el sistema devuelve error 500.',
            'priority' => PriorityTicketEnum::HIGH->value,
            'state' => StateTicketEnum::OPEN->value,
        ]);

        $this->assertTrue(session()->has('message'));
    }

    public function test_it_rejects_store_when_client_is_inactive(): void
    {
        $inactiveClientId = $this->createClient('Ana', 'Inactiva', 'ana.ticket.store@example.com', StateEnum::INACTIVE);

        Livewire::test(AddTicket::class)
            ->set('clientId', (string) $inactiveClientId)
            ->set('subject', 'Problema de acceso')
            ->set('description', 'No puede acceder al sistema')
            ->set('priority', PriorityTicketEnum::MEDIUM->value)
            ->call('saveTicket')
            ->assertHasErrors(['clientId'])
            ->assertSee('El cliente debe existir y estar activo.')
            ->assertNoRedirect();

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_it_rejects_store_when_product_is_not_available(): void
    {
        $clientId = $this->createClient('Carlos', 'Lopez', 'carlos.ticket.store@example.com', StateEnum::ACTIVE);
        $categoryId = $this->createCategory('ERP');
        $productId = $this->createProduct($categoryId, 'ERP Sin Stock', StateProductEnum::OUT_OF_STOCK);

        Livewire::test(AddTicket::class)
            ->set('clientId', (string) $clientId)
            ->set('productId', (string) $productId)
            ->set('subject', 'Incidente producto')
            ->set('description', 'Falla vinculada al producto')
            ->set('priority', PriorityTicketEnum::LOW->value)
            ->call('saveTicket')
            ->assertHasErrors(['productId'])
            ->assertSee('El producto debe existir y estar disponible.')
            ->assertNoRedirect();

        $this->assertDatabaseCount('tickets', 0);
    }

    private function createClient(
        string $firstname,
        string $lastname,
        string $email,
        StateEnum $state,
    ): int {
        return \App\Models\Client::query()->insertGetId([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phone' => '123456789',
            'address' => 'Calle 123',
            'company' => 'GP2',
            'state' => $state->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }

    private function createCategory(string $name): int
    {
        return \App\Models\Category::query()->insertGetId([
            'name' => $name,
            'description' => 'Categoria para tests',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createProduct(int $categoryId, string $name, StateProductEnum $status): int
    {
        return \App\Models\Product::query()->insertGetId([
            'category_id' => $categoryId,
            'name' => $name,
            'description' => 'Producto para tests',
            'unit_price' => 100,
            'status' => $status->value,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }
}
