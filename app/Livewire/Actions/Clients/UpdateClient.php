<?php

namespace App\Livewire\Actions\Clients;

use App\Livewire\Actions\BaseUpdateAction;
use App\Models\Client;

final readonly class UpdateClient extends BaseUpdateAction
{
    /**
     * @return class-string<Client>
     */
    protected function modelClass(): string
    {
        return Client::class;
    }
}
