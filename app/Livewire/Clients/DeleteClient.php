<?php

namespace App\Livewire\Clients;

use App\Livewire\Actions\Clients\DeactivateClient;
use App\Livewire\Actions\Clients\ActivateClient;
use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;
use Livewire\Attributes\Locked;
use App\Enums\StateEnum as StateEnum;

class DeleteClient extends Component
{
    #[Locked]
    public int $clientId;
    public string $clientName = '';
    public ?int $clientUpgradeId = null;
    public bool $isDelete = true;

    public function mount(?Client $client = null): void
    {
        if (! $client || !$client->exists) {
            return;
        }

        $this->clientId = $client->id;
        $this->clientUpgradeId = $client->id;
        $this->clientName = trim("{$client->firstname} {$client->lastname}");
        $this->isDelete = $client->state === StateEnum::ACTIVE;
    }

    public function unmount(): void
    {
        $this->resetConfirmationState();
    }

    public function render()
    {
        return view('clients.delete-client');
    }

    public function cancelAction() : void
    {
        $this->dispatch('toggle-visible');
    }

    public function confirmAction(DeactivateClient $deactivateClient,ActivateClient $activateClient): void
    {
        if (! $this->clientUpgradeId) {
            $this->dispatch('show-message', 'No se selecciono ningun cliente.');
            $this->dispatch('toggle-visible');

            return;
        }

        try {
            ($this->isDelete ? $deactivateClient : $activateClient)($this->clientUpgradeId);
            $this->dispatch('show-message', 'Cliente dado de ' . ($this->isDelete ? 'baja' : 'alta') . ' correctamente.');
        } catch (ModelNotFoundException) {
            $this->dispatch('show-message', 'El cliente seleccionado no existe.');
        }

        $this->dispatch('toggle-visible');
    }

    private function resetConfirmationState(): void
    {
        $this->clientUpgradeId = null;
        $this->clientName = '';
    }
}