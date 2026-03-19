<?php

namespace App\Livewire\Clients;

use App\Enums\StateEnum;
use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Livewire\Actions\Clients\ListeringClient;

class IndexClient extends Component
{
    use WithPagination;

    public string $search = '';
    public string $stateFilter = StateEnum::ACTIVE->value;
    public bool $isVisible;

    public function mount(): void
    {
        $this->isVisible = false;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedstateFilter(): void
    {
        $this->resetPage();
    }

    #[On('toggle-visible')]
    public function toggleVisible()
    {
        $this->isVisible = !$this->isVisible;
    }

    #[on('show-message')]
    public function showMessage(string $message) : void
    {
        session()->flash('message', $message);
    }

    public function render(ListeringClient $listeringClient)
    {
        $clients = $listeringClient($this->stateFilter, $this->search);

        return view('clients.index-client', [
            'clients' => $clients,
        ]);
    }
}
