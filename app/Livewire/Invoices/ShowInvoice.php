<?php

namespace App\Livewire\Invoices;

use App\Enums\StateInvoiceEnum;
use App\Livewire\Actions\Invoices\MarkInvoiceAsPaid;
use App\Livewire\Actions\Invoices\VoidInvoice;
use App\Models\Invoice;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;

class ShowInvoice extends Component
{
    public Invoice $invoice;

    public bool $isVoidModalVisible = false;

    public ?int $invoiceIdToVoid = null;

    private const INVOICE_RELATIONS = [
        'order:id,client_id,date,state,total,observations',
        'order.client:id,firstname,lastname,email,phone,company',
    ];

    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice->load(self::INVOICE_RELATIONS);
    }

    public function markAsPaid(MarkInvoiceAsPaid $markInvoiceAsPaid): void
    {
        try {
            $markInvoiceAsPaid($this->invoice->id);
            session()->flash('message', 'Factura marcada como pagada correctamente.');
            $this->reloadInvoice();
        } catch (ModelNotFoundException) {
            session()->flash('error', 'La factura seleccionada no existe.');
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function openVoidModal(): void
    {
        if ($this->invoice->state === StateInvoiceEnum::PAID) {
            session()->flash('error', 'No se puede anular una factura ya pagada.');
            return;
        }

        if ($this->invoice->state === StateInvoiceEnum::VOIDED) {
            session()->flash('error', 'La factura ya se encuentra anulada.');
            return;
        }

        if ($this->invoice->state !== StateInvoiceEnum::ISSUED) {
            session()->flash('error', 'Solo se puede anular una factura en estado Emitida.');
            return;
        }

        $this->invoiceIdToVoid = $this->invoice->id;
        $this->isVoidModalVisible = true;
    }

    public function cancelVoidAction(): void
    {
        $this->resetVoidState();
    }

    public function confirmVoid(VoidInvoice $voidInvoice): void
    {
        if (! $this->invoiceIdToVoid) {
            session()->flash('error', 'No se selecciono ninguna factura para anular.');
            $this->resetVoidState();
            return;
        }

        try {
            $voidInvoice($this->invoiceIdToVoid);
            session()->flash('message', 'Factura anulada correctamente.');
            $this->reloadInvoice();
        } catch (ModelNotFoundException) {
            session()->flash('error', 'La factura seleccionada no existe.');
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
        }

        $this->resetVoidState();
    }

    public function render()
    {
        return view('invoices.show-invoice');
    }

    private function reloadInvoice(): void
    {
        $this->invoice = $this->invoice->refresh()->load(self::INVOICE_RELATIONS);
    }

    private function resetVoidState(): void
    {
        $this->isVoidModalVisible = false;
        $this->invoiceIdToVoid = null;
    }
}
