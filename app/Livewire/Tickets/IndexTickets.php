<?php

namespace App\Livewire\Tickets;

use App\Enums\RoleEnum;
use App\Enums\PriorityTicketEnum;
use App\Enums\StateTicketEnum;
use App\Livewire\Actions\Tickets\ChangeTicketPriority;
use App\Livewire\Actions\Tickets\ChangeTicketState;
use App\Livewire\Actions\Tickets\ListingTicket;
use App\Models\Product;
use App\Models\Ticket;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

final class IndexTickets extends Component
{
    use WithPagination;

    public string $search = '';
    public string $priorityFilter = '';
    public string $stateFilter = '';
    public string $productFilter = '';
    public string $fromDate = '';
    public string $toDate = '';
    public bool $isStateModalVisible = false;
    public ?int $ticketIdForStateAction = null;
    public string $pendingStateValue = '';
    public string $stateModalTitle = 'Confirmar cambio de estado';
    public string $stateModalMessage = 'Desea continuar con esta accion?';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStateFilter(): void
    {
        $this->resetPage();
    }

    public function updatedProductFilter(): void
    {
        $this->resetPage();
    }

    public function updatedFromDate(): void
    {
        $this->resetPage();
    }

    public function updatedToDate(): void
    {
        $this->resetPage();
    }

    public function changePriority(int $ticketId, string $nextPriority, ChangeTicketPriority $changeTicketPriority): void
    {
        if (! $this->canManageTicket()) {
            session()->flash('error', 'Solo usuarios de soporte o administracion pueden gestionar tickets.');

            return;
        }

        $targetPriority = PriorityTicketEnum::tryFrom($nextPriority);

        if (! $targetPriority) {
            session()->flash('error', 'La prioridad destino no es valida.');

            return;
        }

        try {
            $changeTicketPriority($ticketId, $targetPriority);
            session()->flash('message', 'Prioridad del ticket actualizada correctamente.');
        } catch (ModelNotFoundException) {
            session()->flash('error', 'El ticket seleccionado no existe.');
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function changeState(int $ticketId, string $nextState, ChangeTicketState $changeTicketState): void
    {
        if (! $this->canManageTicket()) {
            session()->flash('error', 'Solo usuarios de soporte o administracion pueden gestionar tickets.');

            return;
        }

        $targetState = StateTicketEnum::tryFrom($nextState);

        if (! $targetState) {
            session()->flash('error', 'El estado destino no es valido.');

            return;
        }

        $this->applyStateChange($ticketId, $targetState, $changeTicketState);
    }

    public function openStateModal(int $ticketId, string $nextState): void
    {
        if (! $this->canManageTicket()) {
            session()->flash('error', 'Solo usuarios de soporte o administracion pueden gestionar tickets.');

            return;
        }

        $targetState = StateTicketEnum::tryFrom($nextState);

        if (! $targetState) {
            session()->flash('error', 'El estado destino no es valido.');

            return;
        }

        $ticket = Ticket::query()->find($ticketId, ['id', 'state']);

        if (! $ticket) {
            session()->flash('error', 'El ticket seleccionado no existe.');

            return;
        }

        $isClose = $targetState === StateTicketEnum::CLOSED;
        $isReopen = $targetState === StateTicketEnum::IN_PROGRESS
            && in_array($ticket->state, [StateTicketEnum::RESOLVED, StateTicketEnum::CLOSED], true);

        if (! $isClose && ! $isReopen) {
            session()->flash('error', 'Esta accion no requiere confirmacion.');

            return;
        }

        $this->ticketIdForStateAction = $ticketId;
        $this->pendingStateValue = $targetState->value;
        $this->isStateModalVisible = true;

        if ($isClose) {
            $this->stateModalTitle = 'Confirmar cierre de ticket';
            $this->stateModalMessage = "El ticket #{$ticketId} cambiara a Cerrado. Desea continuar?";

            return;
        }

        $this->stateModalTitle = 'Confirmar reapertura de ticket';
        $this->stateModalMessage = "El ticket #{$ticketId} sera reabierto y pasara a En progreso. Desea continuar?";
    }

    public function cancelStateChange(): void
    {
        $this->resetStateModal();
    }

    public function confirmStateChange(ChangeTicketState $changeTicketState): void
    {
        if (! $this->canManageTicket()) {
            session()->flash('error', 'Solo usuarios de soporte o administracion pueden gestionar tickets.');
            $this->resetStateModal();

            return;
        }

        if (! $this->ticketIdForStateAction || $this->pendingStateValue === '') {
            session()->flash('error', 'No se encontro una accion de estado pendiente.');
            $this->resetStateModal();

            return;
        }

        $targetState = StateTicketEnum::tryFrom($this->pendingStateValue);

        if (! $targetState) {
            session()->flash('error', 'El estado destino no es valido.');
            $this->resetStateModal();

            return;
        }

        $this->applyStateChange($this->ticketIdForStateAction, $targetState, $changeTicketState);
        $this->resetStateModal();
    }

    public function render(ListingTicket $listingTicket): View
    {
        return view('tickets.index-tickets', [
            'tickets' => $listingTicket(
                search: $this->search,
                priorityFilter: $this->priorityFilter,
                stateFilter: $this->stateFilter,
                productFilter: $this->productFilter,
                fromDate: $this->fromDate,
                toDate: $this->toDate,
            ),
            'priorities' => PriorityTicketEnum::cases(),
            'states' => StateTicketEnum::cases(),
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function applyStateChange(int $ticketId, StateTicketEnum $targetState, ChangeTicketState $changeTicketState): void
    {
        try {
            $changeTicketState($ticketId, $targetState);
            session()->flash('message', 'Estado del ticket actualizado correctamente.');
        } catch (ModelNotFoundException) {
            session()->flash('error', 'El ticket seleccionado no existe.');
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    private function resetStateModal(): void
    {
        $this->isStateModalVisible = false;
        $this->ticketIdForStateAction = null;
        $this->pendingStateValue = '';
        $this->stateModalTitle = 'Confirmar cambio de estado';
        $this->stateModalMessage = 'Desea continuar con esta accion?';
    }

    private function canManageTicket(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return in_array($user->role, [RoleEnum::SUPPORT, RoleEnum::ADMIN], true);
    }
}
