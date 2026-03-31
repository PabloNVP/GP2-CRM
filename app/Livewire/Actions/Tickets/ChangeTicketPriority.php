<?php

namespace App\Livewire\Actions\Tickets;

use App\Enums\PriorityTicketEnum;
use App\Models\Ticket;
use DomainException;

final readonly class ChangeTicketPriority
{
    public function __invoke(int $ticketId, PriorityTicketEnum $targetPriority): Ticket
    {
        $ticket = Ticket::query()->findOrFail($ticketId);

        if ($ticket->priority === $targetPriority) {
            throw new DomainException('El ticket ya tiene la prioridad seleccionada.');
        }

        $ticket->update([
            'priority' => $targetPriority->value,
        ]);

        return $ticket->refresh();
    }
}
