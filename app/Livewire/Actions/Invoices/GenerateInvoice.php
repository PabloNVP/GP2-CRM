<?php

namespace App\Livewire\Actions\Invoices;

use App\Enums\StateInvoiceEnum;
use App\Enums\StateOrderEnum;
use App\Models\Invoice;
use App\Models\Order;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final readonly class GenerateInvoice
{
    /**
     * @throws ModelNotFoundException
     * @throws DomainException
     */
    public function __invoke(int $orderId): Invoice
    {
        /** @var Invoice $invoice */
        $invoice = DB::transaction(function () use ($orderId): Invoice {
            $order = Order::query()
                ->lockForUpdate()
                ->find($orderId);

            if (! $order) {
                throw (new ModelNotFoundException())->setModel(Order::class, [$orderId]);
            }

            if ($order->state !== StateOrderEnum::DELIVERED) {
                throw new DomainException('Solo se puede emitir factura para una orden en estado Entregado.');
            }

            if ($order->invoice()->exists()) {
                throw new DomainException('La orden seleccionada ya tiene una factura emitida.');
            }

            return Invoice::query()->create([
                'order_id' => $order->id,
                'number' => $this->generateUniqueNumber(),
                'issue_date' => now()->toDateString(),
                'total_amount' => (float) $order->total,
                'state' => StateInvoiceEnum::ISSUED,
            ]);
        });

        return $invoice;
    }

    private function generateUniqueNumber(): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $number = sprintf('FAC-%s-%04d', now()->format('YmdHis'), random_int(0, 9999));

            if (! Invoice::query()->where('number', $number)->exists()) {
                return $number;
            }

            usleep(100000);
        }

        throw new RuntimeException('No se pudo generar un numero de factura unico.');
    }
}
