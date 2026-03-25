<?php

namespace App\Livewire\Actions\Clients;

use App\Livewire\Actions\BaseInsertAction;
use App\Models\Client;

final readonly class InsertClient extends BaseInsertAction
{
    /**
     * @return class-string<Client>
     */
    protected function modelClass(): string
    {
        return Client::class;
    }
}
