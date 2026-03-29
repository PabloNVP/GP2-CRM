<?php

namespace App\Livewire\Actions\Orders;

final readonly class RecalculateOrderTotal
{
    public function lineSubtotal(int $count, float $unitPrice): float
    {
        return round($count * $unitPrice, 2);
    }

    /**
     * @param  array<int, array{count:int, unit_price:float}>  $items
     */
    public function __invoke(array $items): float
    {
        $total = 0.0;

        foreach ($items as $item) {
            $total += $this->lineSubtotal($item['count'], $item['unit_price']);
        }

        return round($total, 2);
    }
}
