<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Component;

class ShowOrder extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        $this->order = $order->load([
            'client:id,firstname,lastname,email,phone,company',
            'details:id,order_id,product_id,count,unit_price,subtotal',
            'details.product:id,name',
        ]);
    }

    public function render()
    {
        return view('orders.show-order');
    }
}
