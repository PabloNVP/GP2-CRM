<?php

namespace Tests\Unit\Orders;

use App\Livewire\Actions\Orders\RecalculateOrderTotal;
use Tests\TestCase;

class RecalculateOrderTotalTest extends TestCase
{
    public function test_it_calculates_line_subtotal_correctly(): void
    {
        $action = new RecalculateOrderTotal();

        $subtotal = $action->lineSubtotal(3, 15.50);

        $this->assertSame(46.50, $subtotal);
    }

    public function test_it_calculates_total_from_multiple_items(): void
    {
        $action = new RecalculateOrderTotal();

        $total = $action([
            ['count' => 2, 'unit_price' => 100.00],
            ['count' => 1, 'unit_price' => 50.00],
            ['count' => 3, 'unit_price' => 10.00],
        ]);

        $this->assertSame(280.00, $total);
    }

    public function test_it_rounds_total_to_two_decimals(): void
    {
        $action = new RecalculateOrderTotal();

        $total = $action([
            ['count' => 1, 'unit_price' => 10.125],
            ['count' => 1, 'unit_price' => 0.335],
        ]);

        $this->assertSame(10.47, $total);
    }
}
