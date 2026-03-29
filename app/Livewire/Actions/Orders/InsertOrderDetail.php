<?php

namespace App\Livewire\Actions\Orders;

use App\Models\OrderDetail;

final readonly class InsertOrderDetail
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __invoke(array $payload): OrderDetail
    {
        return OrderDetail::query()->create($payload);
    }
}
