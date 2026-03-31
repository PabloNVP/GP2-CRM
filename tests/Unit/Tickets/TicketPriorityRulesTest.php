<?php

namespace Tests\Unit\Tickets;

use App\Enums\PriorityTicketEnum;
use App\Enums\StateEnum;
use App\Enums\StateTicketEnum;
use App\Livewire\Actions\Tickets\ChangeTicketPriority;
use App\Models\Client;
use App\Models\Ticket;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketPriorityRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_changes_ticket_priority_successfully(): void
    {
        $ticket = $this->createTicket(PriorityTicketEnum::LOW);

        $action = new ChangeTicketPriority();

        $result = $action($ticket->id, PriorityTicketEnum::CRITICAL);

        $this->assertSame(PriorityTicketEnum::CRITICAL, $result->priority);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'priority' => PriorityTicketEnum::CRITICAL->value,
        ]);
    }

    public function test_it_rejects_priority_change_when_priority_is_the_same(): void
    {
        $ticket = $this->createTicket(PriorityTicketEnum::HIGH);

        $action = new ChangeTicketPriority();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('El ticket ya tiene la prioridad seleccionada.');

        $action($ticket->id, PriorityTicketEnum::HIGH);
    }

    private function createTicket(PriorityTicketEnum $priority): Ticket
    {
        $client = Client::query()->create([
            'firstname' => 'Unit',
            'lastname' => 'Priority',
            'email' => fake()->unique()->safeEmail(),
            'phone' => '123456789',
            'address' => 'Calle 123',
            'company' => 'GP2',
            'state' => StateEnum::ACTIVE->value,
        ]);

        return Ticket::query()->create([
            'client_id' => $client->id,
            'product_id' => null,
            'subject' => 'Reglas de prioridad',
            'description' => 'Ticket para pruebas unitarias de prioridad',
            'priority' => $priority->value,
            'state' => StateTicketEnum::OPEN->value,
        ]);
    }
}
