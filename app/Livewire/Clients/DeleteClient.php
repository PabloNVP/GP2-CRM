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

    public function cancelUpgrade() : void
    {
        $this->dispatch('toggle-visible');
    }

    public function actionClient() : DeactivateClient | ActivateClient
    {
        return ($this->isDelete) ? new DeactivateClient : new ActivateClient;
    }

    public function upgradeClient()
    {
        if (! $this->clientUpgradeId) {
            $this->dispatch('show-message', 'No se selecciono ningun cliente.');
            $this->dispatch('toggle-visible');
        }

        try {
            $this->actionClient()($this->clientUpgradeId);
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