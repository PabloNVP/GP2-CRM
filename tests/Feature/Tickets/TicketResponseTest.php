<?php

namespace Tests\Feature\Tickets;

use App\Enums\PriorityTicketEnum;
use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Enums\StateTicketEnum;
use App\Livewire\Tickets\ShowTicket;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TicketResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_user_can_add_response_and_ticket_moves_to_in_progress(): void
    {
        $supportUser = User::factory()->create([
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $ticket = $this->createTicket(StateTicketEnum::OPEN);

        Livewire::actingAs($supportUser)
            ->test(ShowTicket::class, ['ticket' => $ticket])
            ->set('responseMessage', 'Estamos revisando el incidente.')
            ->call('saveResponse')
            ->assertHasNoErrors()
            ->assertSet('responseMessage', '');

        $this->assertDatabaseHas('ticket_responses', [
            'ticket_id' => $ticket->id,
            'user_id' => $supportUser->id,
            'message' => 'Estamos revisando el incidente.',
        ]);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'state' => StateTicketEnum::IN_PROGRESS->value,
        ]);
    }

    public function test_admin_user_can_add_response(): void
    {
        $adminUser = User::factory()->create([
            'role' => RoleEnum::ADMIN->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $ticket = $this->createTicket(StateTicketEnum::IN_PROGRESS);

        Livewire::actingAs($adminUser)
            ->test(ShowTicket::class, ['ticket' => $ticket])
            ->set('responseMessage', 'Se aplico una solucion temporal.')
            ->call('saveResponse')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ticket_responses', [
            'ticket_id' => $ticket->id,
            'user_id' => $adminUser->id,
            'message' => 'Se aplico una solucion temporal.',
        ]);
    }

    public function test_operator_user_cannot_add_response(): void
    {
        $operatorUser = User::factory()->create([
            'role' => RoleEnum::OPERATOR->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $ticket = $this->createTicket(StateTicketEnum::OPEN);

        Livewire::actingAs($operatorUser)
            ->test(ShowTicket::class, ['ticket' => $ticket])
            ->set('responseMessage', 'Intento de respuesta sin permiso')
            ->call('saveResponse')
            ->assertSee('Solo usuarios de soporte o administracion pueden responder tickets.');

        $this->assertDatabaseCount('ticket_responses', 0);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'state' => StateTicketEnum::OPEN->value,
        ]);
    }

    public function test_it_requires_non_empty_response_message(): void
    {
        $supportUser = User::factory()->create([
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $ticket = $this->createTicket(StateTicketEnum::OPEN);

        Livewire::actingAs($supportUser)
            ->test(ShowTicket::class, ['ticket' => $ticket])
            ->set('responseMessage', '   ')
            ->call('saveResponse')
            ->assertHasErrors(['responseMessage' => 'required']);

        $this->assertDatabaseCount('ticket_responses', 0);
    }

    private function createTicket(StateTicketEnum $state): Ticket
    {
        $client = Client::query()->create([
            'firstname' => 'Lucia',
            'lastname' => 'Suarez',
            'email' => fake()->unique()->safeEmail(),
            'phone' => '11223344',
            'address' => 'Calle 123',
            'company' => 'Empresa Test',
            'state' => StateEnum::ACTIVE->value,
        ]);

        return Ticket::query()->create([
            'client_id' => $client->id,
            'product_id' => null,
            'subject' => 'Error de acceso',
            'description' => 'No puedo ingresar al modulo',
            'priority' => PriorityTicketEnum::MEDIUM->value,
            'state' => $state->value,
        ]);
    }
}
