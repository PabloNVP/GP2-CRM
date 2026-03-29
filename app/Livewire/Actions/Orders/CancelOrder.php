<?php

namespace App\Livewire\Actions\Orders;

use App\Enums\StateOrderEnum;
use App\Models\Order;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class CancelOrder
{
    /**
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function __invoke(int $orderId): bool
    {
        $order = Order::query()->find($orderId);

        if (! $order) {
            throw (new ModelNotFoundException())->setModel(Order::class, [$orderId]);
        }

        if (! in_array($order->state, [StateOrderEnum::PENDING, StateOrderEnum::PROCESSING], true)) {
            throw new DomainException('Solo se puede cancelar una orden en estado Pendiente o En proceso.');
        }

        return $order->update([
            'state' => StateOrderEnum::CANCELLED,
        ]);
    }
}
