<?php

namespace Tests\Unit\Tickets;

use App\Enums\PriorityTicketEnum;
use App\Enums\StateEnum;
use App\Enums\StateTicketEnum;
use App\Livewire\Actions\Tickets\ChangeTicketState;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TicketStateRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_allows_transition_from_open_to_in_progress(): void
    {
        $ticket = $this->createTicket(StateTicketEnum::OPEN);

        $action = new ChangeTicketState();

        $result = $action($ticket->id, StateTicketEnum::IN_PROGRESS);

        $this->assertSame(StateTicketEnum::IN_PROGRESS, $result->state);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'state' => StateTicketEnum::IN_PROGRESS->value,
        ]);
    }

    public function test_it_rejects_invalid_transition_from_open_to_closed(): void
    {
        $ticket = $this->createTicket(StateTicketEnum::OPEN);

        $action = new ChangeTicketState();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Transicion de estado invalida para el ticket seleccionado.');

        $action($ticket->id, StateTicketEnum::CLOSED);
    }

    public function test_it_rejects_closing_ticket_without_responses(): void
    {
        $ticket = $this->createTicket(StateTicketEnum::RESOLVED);

        $action = new ChangeTicketState();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('No se puede cerrar un ticket sin al menos una respuesta.');

        $action($ticket->id, StateTicketEnum::CLOSED);
    }

    public function test_it_allows_closing_ticket_with_at_least_one_response(): void
    {
        $ticket = $this->createTicket(StateTicketEnum::RESOLVED);
        $user = User::factory()->create([
            'state' => StateEnum::ACTIVE->value,
        ]);

        DB::table('ticket_responses')->insert([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'Respuesta para habilitar cierre.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $action = new ChangeTicketState();

        $result = $action($ticket->id, StateTicketEnum::CLOSED);

        $this->assertSame(StateTicketEnum::CLOSED, $result->state);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'state' => StateTicketEnum::CLOSED->value,
        ]);
    }

    private function createTicket(StateTicketEnum $state): Ticket
    {
        $client = Client::query()->create([
            'firstname' => 'Unit',
            'lastname' => 'Ticket',
            'email' => fake()->unique()->safeEmail(),
            'phone' => '123456789',
            'address' => 'Calle 123',
            'company' => 'GP2',
            'state' => StateEnum::ACTIVE->value,
        ]);

        return Ticket::query()->create([
            'client_id' => $client->id,
            'product_id' => null,
            'subject' => 'Reglas de estado',
            'description' => 'Ticket para pruebas unitarias de estado',
            'priority' => PriorityTicketEnum::MEDIUM->value,
            'state' => $state->value,
        ]);
    }
}
