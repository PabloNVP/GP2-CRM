<?php

namespace Tests\Feature\Tickets;

use App\Enums\PriorityTicketEnum;
use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Enums\StateTicketEnum;
use App\Livewire\Tickets\IndexTickets;
use App\Livewire\Tickets\ShowTicket;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class TicketStateTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_user_can_change_priority_and_state_from_listing(): void
    {
        $supportUser = User::factory()->create([
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $ticket = $this->createTicket(StateTicketEnum::OPEN, PriorityTicketEnum::MEDIUM);

        Livewire::actingAs($supportUser)
            ->test(IndexTickets::class)
            ->call('changePriority', $ticket->id, PriorityTicketEnum::CRITICAL->value)
            ->assertSee('Prioridad del ticket actualizada correctamente.')
            ->call('changeState', $ticket->id, StateTicketEnum::IN_PROGRESS->value)
            ->assertSee('Estado del ticket actualizado correctamente.');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'priority' => PriorityTicketEnum::CRITICAL->value,
            'state' => StateTicketEnum::IN_PROGRESS->value,
        ]);
    }

    public function test_detail_flow_allows_valid_transitions_with_close_and_reopen_confirmation(): void
    {
        $supportUser = User::factory()->create([
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $ticket = $this->createTicket(StateTicketEnum::OPEN, PriorityTicketEnum::HIGH);

        $this->createResponse($ticket->id, $supportUser->id, 'Respuesta inicial para permitir cierre.');

        Livewire::actingAs($supportUser)
            ->test(ShowTicket::class, ['ticket' => $ticket])
            ->call('changeState', StateTicketEnum::IN_PROGRESS->value)
            ->assertSee('Estado del ticket actualizado correctamente.')
            ->call('changeState', StateTicketEnum::RESOLVED->value)
            ->assertSee('Estado del ticket actualizado correctamente.')
            ->call('openStateModal', StateTicketEnum::CLOSED->value)
            ->assertSet('isStateModalVisible', true)
            ->call('confirmStateChange')
            ->assertSet('isStateModalVisible', false)
            ->assertSee('Estado del ticket actualizado correctamente.')
            ->call('openStateModal', StateTicketEnum::IN_PROGRESS->value)
            ->assertSet('isStateModalVisible', true)
            ->call('confirmStateChange')
            ->assertSet('isStateModalVisible', false)
            ->assertSee('Estado del ticket actualizado correctamente.');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'state' => StateTicketEnum::IN_PROGRESS->value,
        ]);
    }

    public function test_it_rejects_closing_ticket_without_responses(): void
    {
        $supportUser = User::factory()->create([
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $ticket = $this->createTicket(StateTicketEnum::RESOLVED, PriorityTicketEnum::MEDIUM);

        Livewire::actingAs($supportUser)
            ->test(ShowTicket::class, ['ticket' => $ticket])
            ->call('openStateModal', StateTicketEnum::CLOSED->value)
            ->assertSet('isStateModalVisible', true)
            ->call('confirmStateChange')
            ->assertSet('isStateModalVisible', false)
            ->assertSee('No se puede cerrar un ticket sin al menos una respuesta.');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'state' => StateTicketEnum::RESOLVED->value,
        ]);
    }

    public function test_it_rejects_invalid_state_transition(): void
    {
        $supportUser = User::factory()->create([
            'role' => RoleEnum::SUPPORT->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $ticket = $this->createTicket(StateTicketEnum::OPEN, PriorityTicketEnum::LOW);

        Livewire::actingAs($supportUser)
            ->test(ShowTicket::class, ['ticket' => $ticket])
            ->call('changeState', StateTicketEnum::CLOSED->value)
            ->assertSee('Transicion de estado invalida para el ticket seleccionado.');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'state' => StateTicketEnum::OPEN->value,
        ]);
    }

    public function test_operator_cannot_manage_ticket_state_or_priority(): void
    {
        $operatorUser = User::factory()->create([
            'role' => RoleEnum::OPERATOR->value,
            'state' => StateEnum::ACTIVE->value,
        ]);

        $ticket = $this->createTicket(StateTicketEnum::OPEN, PriorityTicketEnum::MEDIUM);

        Livewire::actingAs($operatorUser)
            ->test(ShowTicket::class, ['ticket' => $ticket])
            ->call('changePriority', PriorityTicketEnum::CRITICAL->value)
            ->assertSee('Solo usuarios de soporte o administracion pueden gestionar tickets.')
            ->call('changeState', StateTicketEnum::IN_PROGRESS->value)
            ->assertSee('Solo usuarios de soporte o administracion pueden gestionar tickets.');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'priority' => PriorityTicketEnum::MEDIUM->value,
            'state' => StateTicketEnum::OPEN->value,
        ]);
    }

    private function createTicket(StateTicketEnum $state, PriorityTicketEnum $priority): Ticket
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
            'subject' => 'Seguimiento de incidencia',
            'description' => 'Descripcion de prueba para transiciones',
            'priority' => $priority->value,
            'state' => $state->value,
        ]);
    }

    private function createResponse(int $ticketId, int $userId, string $message): void
    {
        DB::table('ticket_responses')->insert([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'message' => $message,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
