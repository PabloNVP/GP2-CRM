<?php

namespace App\Livewire\Actions\Clients;

use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpsertClient
{
    /**
     * Crea o actualiza un cliente específico.
     *
     * @param  array<string, mixed>  $payload
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function __invoke(array $payload, ?int $clientId = null): bool
    {
        if ($clientId === null) {
            return Client::query()->create($payload) !== null;
        }

        $client = Client::query()->find($clientId);

        if (! $client) {
            throw (new ModelNotFoundException())->setModel(Client::class, [$clientId]);
        }

        $client->fill($payload);

        // Do not report an error when edit submits identical data.
        return $client->isDirty() ? $client->save() : true;
    }
}
