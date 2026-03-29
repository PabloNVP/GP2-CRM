<?php

namespace App\Livewire\Actions\Orders;

use App\Models\Order;

final readonly class InsertOrder
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __invoke(array $payload): Order
    {
        return Order::query()->create($payload);
    }
}
