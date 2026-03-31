<?php

namespace App\Livewire\Actions\Tickets;

use App\Models\Ticket;

final readonly class CreateTicket
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __invoke(array $payload): Ticket
    {
        return Ticket::query()->create($payload);
    }
}
