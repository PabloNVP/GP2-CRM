<?php

namespace App\Livewire\Clients;

use App\Livewire\Actions\Clients\DeactivateClient;
use App\Livewire\Actions\Clients\ActivateClient;
use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;
use App\Enums\StateEnum as StateEnum;

class DeleteClient extends Component
{
    public int $clientId;
    public StateEnum $clientState;
    public string $clientName = '';
    public bool $confirming = false;
    public ?int $clientUpgradeId = null;
    public bool $isDelete = true;

    public function mount(?Client $client = null, mixed $isDelete = 1): void
    {
        if (! $client || ! $client->exists) {
            return;
        }

        $this->clientId = $client->id;
        $this->clientState = $client->state;
        $this->clientUpgradeId = $client->id;
        $this->clientName = trim("{$client->firstname} {$client->lastname}");
        $this->confirming = true;
        $this->isDelete = filter_var($isDelete, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? ((int) $isDelete === 1);
    }

    public function render()
    {
        return view('clients.delete-client');
    }

    public function confirmDeactivate(int $clientId): void
    {
        $client = Client::find($clientId);

        if (! $client) {
            session()->flash('error', 'El cliente seleccionado no existe.');
            return;
        }

        $this->clientUpgradeId = $client->id;
        $this->clientName = trim("{$client->firstname} {$client->lastname}");
        $this->confirming = true;
    }

    public function cancelUpgrade()
    {
        $this->resetConfirmationState();

        return redirect()->route('clients.index');
    }

    public function upgradeClient()
    {
        if ($this->isDelete) {
            return $this->deactivateClient(new DeactivateClient);
        }

        return $this->activateClient(new ActivateClient);
    }

    public function deactivateClient(DeactivateClient $deactivateClient)
    {
        if (! $this->clientUpgradeId) {
            session()->flash('error', 'No se selecciono ningun cliente.');

            return redirect()->route('clients.index');
        }

        try {
            $deactivateClient($this->clientUpgradeId);
            session()->flash('message', 'Cliente dado de baja correctamente.');
        } catch (ModelNotFoundException) {
            $this->resetConfirmationState();
            session()->flash('error', 'El cliente seleccionado no existe.');

            return redirect()->route('clients.index');
        }

        $this->resetConfirmationState();

        return redirect()->route('clients.index');
    }

    public function activateClient(ActivateClient $activateClient)
    {
        if (! $this->clientUpgradeId) {
            session()->flash('error', 'No se selecciono ningun cliente.');

            return redirect()->route('clients.index');
        }

        try 
        {
            $activateClient($this->clientUpgradeId);
            session()->flash('message', 'Cliente dado de alta nuevamente correctamente.');
        } catch (ModelNotFoundException) {
            $this->resetConfirmationState();
            session()->flash('error', 'El cliente seleccionado no existe.');

            return redirect()->route('clients.index');
        }

        $this->resetConfirmationState();

        return redirect()->route('clients.index');
    }

    private function resetConfirmationState(): void
    {
        $this->confirming = false;
        $this->clientUpgradeId = null;
        $this->clientName = '';
    }
}