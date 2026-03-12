<?php

namespace App\Livewire\Actions\Clients;

use App\Enums\StateEnum;
use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class DeactivateClient
{
    /**
     * Desactiva un cliente específico.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function __invoke(int $clientId): bool
    {
        $client = Client::query()->find($clientId);

        if (! $client) {
            throw (new ModelNotFoundException())->setModel(Client::class, [$clientId]);
        }

        return $client->update([
            'state' => StateEnum::INACTIVE,
        ]);
    }
}
