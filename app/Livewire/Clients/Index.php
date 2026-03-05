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
    public bool $confirmingDeletion = false;
    public ?int $clientToDeactivateId = null;
    public string $clientToDeactivateName = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStateFilter(): void
    {
        $this->resetPage();
    }

    public function confirmDeactivate(int $clientId): void
    {
        $client = Client::find($clientId);

        if (! $client) {
            session()->flash('error', 'El cliente seleccionado no existe.');
            return;
        }

        $this->clientToDeactivateId = $client->id;
        $this->clientToDeactivateName = trim("{$client->firstname} {$client->lastname}");
        $this->confirmingDeletion = true;
    }

    public function cancelDeactivate(): void
    {
        $this->confirmingDeletion = false;
        $this->clientToDeactivateId = null;
        $this->clientToDeactivateName = '';
    }

    public function deactivateClient(): void
    {
        if (! $this->clientToDeactivateId) {
            session()->flash('error', 'No se selecciono ningun cliente para eliminar.');
            return;
        }

        $client = Client::find($this->clientToDeactivateId);

        if (! $client) {
            $this->cancelDeactivate();
            session()->flash('error', 'El cliente seleccionado no existe.');
            return;
        }

        $client->update([
            'state' => StateEnum::INACTIVE->value,
        ]);

        $this->cancelDeactivate();
        session()->flash('message', 'Cliente dado de baja correctamente.');
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
