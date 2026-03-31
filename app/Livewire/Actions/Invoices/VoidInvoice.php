<?php

namespace App\Livewire\Actions\Invoices;

use App\Enums\StateInvoiceEnum;
use App\Models\Invoice;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

final readonly class VoidInvoice
{
    /**
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function __invoke(int $invoiceId): bool
    {
        return (bool) DB::transaction(function () use ($invoiceId): bool {
            $invoice = Invoice::query()
                ->lockForUpdate()
                ->find($invoiceId);

            if (! $invoice) {
                throw (new ModelNotFoundException())->setModel(Invoice::class, [$invoiceId]);
            }

            if ($invoice->state === StateInvoiceEnum::PAID) {
                throw new DomainException('No se puede anular una factura ya pagada.');
            }

            if ($invoice->state === StateInvoiceEnum::VOIDED) {
                throw new DomainException('La factura ya se encuentra anulada.');
            }

            if ($invoice->state !== StateInvoiceEnum::ISSUED) {
                throw new DomainException('Solo se puede anular una factura en estado Emitida.');
            }

            return $invoice->update([
                'state' => StateInvoiceEnum::VOIDED,
            ]);
        });
    }
}
