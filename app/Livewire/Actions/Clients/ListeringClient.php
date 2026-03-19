<?php

namespace App\Livewire\Actions\Clients;

use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class ListeringClient
{
    /**
     * Lista los clientes activos.
     *
     * @return \Illuminate\Database\Eloquent\Collection|Client[]
     */
    public function __invoke($stateFilter = '', $search = ''): iterable
    {
        $query = Client::query()->orderByDesc('id');

        if ($stateFilter !== '') {
            $query->where('state', $stateFilter);
        }

        $search = trim($search);

        if (mb_strlen($search) >= 3) {
            $query->where(function ($subQuery) use ($search): void {
                $subQuery->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate(10);
    }
}