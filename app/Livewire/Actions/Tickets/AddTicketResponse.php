<?php

namespace App\Livewire\Actions\Tickets;

use App\Enums\StateTicketEnum;
use App\Models\Ticket;
use App\Models\TicketResponse;
use Illuminate\Support\Facades\DB;

final readonly class AddTicketResponse
{
    public function __invoke(int $ticketId, int $userId, string $message): TicketResponse
    {
        return DB::transaction(function () use ($ticketId, $userId, $message): TicketResponse {
            $ticket = Ticket::query()->lockForUpdate()->findOrFail($ticketId);

            $response = TicketResponse::query()->create([
                'ticket_id' => $ticket->id,
                'user_id' => $userId,
                'message' => $message,
            ]);

            if ($ticket->state === StateTicketEnum::OPEN) {
                $ticket->update([
                    'state' => StateTicketEnum::IN_PROGRESS->value,
                ]);
            }

            return $response;
        });
    }
}
