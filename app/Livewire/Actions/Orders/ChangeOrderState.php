<?php

namespace App\Livewire\Actions\Orders;

use App\Enums\StateOrderEnum;
use App\Models\Order;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class ChangeOrderState
{
    /**
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function __invoke(int $orderId, StateOrderEnum $nextState): bool
    {
        $order = Order::query()->find($orderId);

        if (! $order) {
            throw (new ModelNotFoundException())->setModel(Order::class, [$orderId]);
        }

        if ($order->state === StateOrderEnum::SHIPPED || $order->state === StateOrderEnum::DELIVERED) {
            if ($nextState === StateOrderEnum::PENDING) {
                throw new DomainException('No se puede volver a Pendiente una orden ya Enviada o Entregada.');
            }
        }

        $allowedTransitions = [
            StateOrderEnum::PENDING->value => [StateOrderEnum::PROCESSING->value],
            StateOrderEnum::PROCESSING->value => [StateOrderEnum::SHIPPED->value],
            StateOrderEnum::SHIPPED->value => [StateOrderEnum::DELIVERED->value],
        ];

        $currentState = $order->state->value;
        $isAllowed = in_array(
            $nextState->value,
            $allowedTransitions[$currentState] ?? [],
            true,
        );

        if (! $isAllowed) {
            throw new DomainException('Transicion de estado invalida para la orden seleccionada.');
        }

        return $order->update([
            'state' => $nextState,
        ]);
    }
}
