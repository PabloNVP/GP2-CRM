<?php

namespace App\Livewire\Tickets;

use App\Enums\PriorityTicketEnum;
use App\Enums\RoleEnum;
use App\Enums\StateTicketEnum;
use App\Livewire\Actions\Tickets\AddTicketResponse;
use App\Livewire\Actions\Tickets\ChangeTicketPriority;
use App\Livewire\Actions\Tickets\ChangeTicketState;
use App\Models\Ticket;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;

class ShowTicket extends Component
{
    public Ticket $ticket;
    public string $responseMessage = '';
    public bool $isStateModalVisible = false;
    public string $pendingStateValue = '';
    public string $stateModalTitle = 'Confirmar cambio de estado';
    public string $stateModalMessage = 'Desea continuar con esta accion?';

    public function mount(Ticket $ticket): void
    {
        $this->ticket = $ticket;
        $this->reloadTicket();
    }

    public function saveResponse(AddTicketResponse $addTicketResponse): void
    {
        $user = auth()->user();

        if (! $user) {
            session()->flash('error', 'Debe iniciar sesion para responder tickets.');

            return;
        }

        if (! in_array($user->role, [RoleEnum::SUPPORT, RoleEnum::ADMIN], true)) {
            session()->flash('error', 'Solo usuarios de soporte o administracion pueden responder tickets.');

            return;
        }

        $this->responseMessage = trim($this->responseMessage);

        $validated = $this->validate([
            'responseMessage' => ['required', 'string'],
        ], [
            'responseMessage.required' => 'La respuesta no puede estar vacia.',
            'responseMessage.string' => 'La respuesta debe ser texto valido.',
        ]);

        try {
            $addTicketResponse(
                ticketId: $this->ticket->id,
                userId: (int) $user->id,
                message: $validated['responseMessage'],
            );
        } catch (ModelNotFoundException) {
            session()->flash('error', 'El ticket seleccionado no existe.');

            return;
        }

        $this->responseMessage = '';
        $this->reloadTicket();

        session()->flash('message', 'Respuesta registrada correctamente.');
    }

    public function changePriority(string $nextPriority, ChangeTicketPriority $changeTicketPriority): void
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
            $changeTicketPriority($this->ticket->id, $targetPriority);
            $this->reloadTicket();
            session()->flash('message', 'Prioridad del ticket actualizada correctamente.');
        } catch (ModelNotFoundException) {
            session()->flash('error', 'El ticket seleccionado no existe.');
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function changeState(string $nextState, ChangeTicketState $changeTicketState): void
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

        $this->applyStateChange($targetState, $changeTicketState);
    }

    public function openStateModal(string $nextState): void
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

        $currentState = $this->ticket->state;
        $isClose = $targetState === StateTicketEnum::CLOSED;
        $isReopen = $targetState === StateTicketEnum::IN_PROGRESS
            && in_array($currentState, [StateTicketEnum::RESOLVED, StateTicketEnum::CLOSED], true);

        if (! $isClose && ! $isReopen) {
            session()->flash('error', 'Esta accion no requiere confirmacion.');

            return;
        }

        $this->pendingStateValue = $targetState->value;
        $this->isStateModalVisible = true;

        if ($isClose) {
            $this->stateModalTitle = 'Confirmar cierre de ticket';
            $this->stateModalMessage = "El ticket #{$this->ticket->id} cambiara a Cerrado. Desea continuar?";

            return;
        }

        $this->stateModalTitle = 'Confirmar reapertura de ticket';
        $this->stateModalMessage = "El ticket #{$this->ticket->id} sera reabierto y pasara a En progreso. Desea continuar?";
    }

    public function cancelStateChange(): void
    {
        $this->resetStateModal();
    }

    public function confirmStateChange(ChangeTicketState $changeTicketState): void
    {
        if ($this->pendingStateValue === '') {
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

        $this->applyStateChange($targetState, $changeTicketState);
        $this->resetStateModal();
    }

    public function render()
    {
        return view('tickets.show-ticket');
    }

    private function reloadTicket(): void
    {
        $this->ticket = $this->ticket->refresh()->load([
            'client:id,firstname,lastname,email,phone,company',
            'product:id,name',
            'responses' => fn ($query) => $query
                ->select(['id', 'ticket_id', 'user_id', 'message', 'created_at'])
                ->orderBy('created_at'),
            'responses.user:id,name',
        ]);
    }

    private function canManageTicket(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return in_array($user->role, [RoleEnum::SUPPORT, RoleEnum::ADMIN], true);
    }

    private function applyStateChange(StateTicketEnum $targetState, ChangeTicketState $changeTicketState): void
    {
        try {
            $changeTicketState($this->ticket->id, $targetState);
            $this->reloadTicket();
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
        $this->pendingStateValue = '';
        $this->stateModalTitle = 'Confirmar cambio de estado';
        $this->stateModalMessage = 'Desea continuar con esta accion?';
    }
}
