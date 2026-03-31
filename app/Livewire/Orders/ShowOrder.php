<?php

namespace App\Livewire\Orders;

use App\Livewire\Actions\Invoices\GenerateInvoice;
use App\Models\Order;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;

class ShowOrder extends Component
{
    public Order $order;

    private const ORDER_RELATIONS = [
        'client:id,firstname,lastname,email,phone,company',
        'details:id,order_id,product_id,count,unit_price,subtotal',
        'details.product:id,name',
        'invoice:id,order_id,number,issue_date,total_amount,state',
    ];

    public function mount(Order $order): void
    {
        $this->order = $order->load(self::ORDER_RELATIONS);
    }

    public function generateInvoice(GenerateInvoice $generateInvoice): void
    {
        try {
            $invoice = $generateInvoice($this->order->id);
            session()->flash('message', 'Factura '.$invoice->number.' emitida correctamente.');
            $this->reloadOrder();
        } catch (ModelNotFoundException) {
            session()->flash('error', 'La orden seleccionada no existe.');
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function render()
    {
        return view('orders.show-order');
    }

    private function reloadOrder(): void
    {
        $this->order = $this->order->refresh()->load(self::ORDER_RELATIONS);
    }
}
