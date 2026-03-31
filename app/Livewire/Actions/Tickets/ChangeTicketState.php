<?php

namespace App\Livewire\Actions\Tickets;

use App\Enums\StateTicketEnum;
use App\Models\Ticket;
use DomainException;
use Illuminate\Support\Facades\DB;

final readonly class ChangeTicketState
{
    public function __invoke(int $ticketId, StateTicketEnum $targetState): Ticket
    {
        return DB::transaction(function () use ($ticketId, $targetState): Ticket {
            $ticket = Ticket::query()
                ->withCount('responses')
                ->lockForUpdate()
                ->findOrFail($ticketId);

            $currentState = $ticket->state;

            if ($currentState === $targetState) {
                throw new DomainException('El ticket ya se encuentra en el estado seleccionado.');
            }

            $allowedTransitions = match ($currentState) {
                StateTicketEnum::OPEN => [StateTicketEnum::IN_PROGRESS],
                StateTicketEnum::IN_PROGRESS => [StateTicketEnum::RESOLVED],
                StateTicketEnum::RESOLVED => [StateTicketEnum::CLOSED, StateTicketEnum::IN_PROGRESS],
                StateTicketEnum::CLOSED => [StateTicketEnum::IN_PROGRESS],
            };

            if (! in_array($targetState, $allowedTransitions, true)) {
                throw new DomainException('Transicion de estado invalida para el ticket seleccionado.');
            }

            if ($targetState === StateTicketEnum::CLOSED && $ticket->responses_count < 1) {
                throw new DomainException('No se puede cerrar un ticket sin al menos una respuesta.');
            }

            $ticket->update([
                'state' => $targetState->value,
            ]);

            return $ticket->refresh();
        });
    }
}
