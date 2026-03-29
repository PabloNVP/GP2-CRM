<?php

namespace App\Livewire\Orders;

use App\Enums\StateOrderEnum;
use App\Livewire\Actions\Orders\CancelOrder;
use App\Livewire\Actions\Orders\ChangeOrderState;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Actions\Orders\ListeringOrder;
use App\Models\Order;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IndexOrders extends Component
{
    use WithPagination;

    public string $search = '';
    public string $stateFilter = '';
    public string $fromDate = '';
    public string $toDate = '';
    public bool $isCancelModalVisible = false;
    public ?int $orderIdToCancel = null;

    public function openCancelModal(int $orderId): void
    {
        $order = Order::query()->find($orderId);

        if (! $order) {
            session()->flash('error', 'La orden seleccionada no existe.');
            return;
        }

        if (! in_array($order->state, [StateOrderEnum::PENDING, StateOrderEnum::PROCESSING], true)) {
            session()->flash('error', 'Solo se puede cancelar una orden en estado Pendiente o En proceso.');
            return;
        }

        $this->orderIdToCancel = $orderId;
        $this->isCancelModalVisible = true;
    }

    public function cancelCancelAction(): void
    {
        $this->resetCancelState();
    }

    public function confirmCancel(CancelOrder $cancelOrder): void
    {
        if (! $this->orderIdToCancel) {
            session()->flash('error', 'No se selecciono ninguna orden para cancelar.');
            $this->resetCancelState();
            return;
        }

        try {
            $cancelOrder($this->orderIdToCancel);
            session()->flash('message', 'Orden cancelada correctamente.');
        } catch (ModelNotFoundException) {
            session()->flash('error', 'La orden seleccionada no existe.');
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
        }

        $this->resetCancelState();
    }

    public function changeState(int $orderId, string $nextState, ChangeOrderState $changeOrderState): void
    {
        $targetState = StateOrderEnum::tryFrom($nextState);

        if (! $targetState) {
            session()->flash('error', 'El estado destino no es valido.');
            return;
        }

        try {
            $changeOrderState($orderId, $targetState);
            session()->flash('message', 'Estado de la orden actualizado correctamente.');
        } catch (ModelNotFoundException) {
            session()->flash('error', 'La orden seleccionada no existe.');
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStateFilter(): void
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

    public function render(ListeringOrder $listeringOrder)
    {
        $orders = $listeringOrder(
            search: $this->search,
            stateFilter: $this->stateFilter,
            fromDate: $this->fromDate,
            toDate: $this->toDate,
        );

        $stateOptions = StateOrderEnum::cases();

        return view('orders.index-orders', compact('orders', 'stateOptions'));
    }

    private function resetCancelState(): void
    {
        $this->isCancelModalVisible = false;
        $this->orderIdToCancel = null;
    }
}
