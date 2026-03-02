<?php

namespace App\Livewire\Clients;

use App\Enums\StateEnum;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $stateFilter = StateEnum::ACTIVE->value;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStateFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Client::query()->orderByDesc('id');

        if ($this->stateFilter !== '') {
            $query->where('state', $this->stateFilter);
        }

        $search = trim($this->search);

        if (mb_strlen($search) >= 3) {
            $query->where(function ($subQuery) use ($search): void {
                $subQuery->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients = $query->paginate(10);

        return view('livewire.clients.index', [
            'clients' => $clients,
        ]);
    }

}
