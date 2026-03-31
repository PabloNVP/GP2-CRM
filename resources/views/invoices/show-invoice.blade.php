@php
use App\Enums\StateInvoiceEnum;
@endphp

<div class="flex flex-col gap-6 w-full">
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <a class="material-icons text-2xl filled-icon cursor-pointer" wire:navigate href="{{ route('invoices.index') }}">
                arrow_back
            </a>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">Detalle de Factura {{ $invoice->number }}</h1>
                <p class="text-sm text-gray-500">Resumen administrativo de la factura emitida.</p>
            </div>
        </div>
    </div>

    @if (session('message'))
        <div class="rounded-md border border-green-200 bg-green-50 px-6 py-4 mx-8 text-sm text-green-700">
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-md border border-red-200 bg-red-50 px-6 py-4 mx-8 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mx-8">
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Cliente</p>
            <p class="text-sm font-medium text-gray-900">{{ trim(($invoice->order?->client?->firstname ?? '').' '.($invoice->order?->client?->lastname ?? '')) ?: '-' }}</p>
            <p class="mt-2 text-sm text-gray-600">{{ $invoice->order?->client?->email ?? '-' }}</p>
            <p class="text-sm text-gray-600">{{ $invoice->order?->client?->phone ?? '-' }}</p>
            <p class="text-sm text-gray-600">{{ $invoice->order?->client?->company ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Factura</p>
            <p class="text-sm font-medium text-gray-900">{{ $invoice->number }}</p>
            <p class="mt-2 text-xs uppercase tracking-wide text-gray-500">Fecha de emision</p>
            <p class="text-sm text-gray-600">{{ $invoice->issue_date?->format('d/m/Y') ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Estado</p>
            <p class="text-sm font-medium text-gray-900">
                <span @class([
                    'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                    'bg-sky-100 text-sky-700' => $invoice->state === StateInvoiceEnum::ISSUED,
                    'bg-emerald-100 text-emerald-700' => $invoice->state === StateInvoiceEnum::PAID,
                    'bg-red-100 text-red-700' => $invoice->state === StateInvoiceEnum::VOIDED,
                ])>
                    {{ $invoice->state->value }}
                </span>
            </p>
            <p class="mt-2 text-xs uppercase tracking-wide text-gray-500">Monto total</p>
            <p class="text-sm font-medium text-gray-900">$ {{ number_format((float) $invoice->total_amount, 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white mx-8">
        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Orden asociada</p>
                <p class="text-sm font-medium text-gray-900">#{{ $invoice->order_id }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Fecha de la orden</p>
                <p class="text-sm text-gray-700">{{ $invoice->order?->date?->format('d/m/Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Accion</p>
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        href="{{ route('orders.show', $invoice->order_id) }}"
                        wire:navigate
                        class="buttonAction edit"
                    >
                        Ver pedido
                    </a>

                    @if ($invoice->state === StateInvoiceEnum::ISSUED)
                        <button
                            type="button"
                            wire:click="markAsPaid"
                            class="buttonAction active"
                        >
                            Registrar pago
                        </button>

                        <button
                            type="button"
                            wire:click="openVoidModal"
                            class="buttonAction deactivate"
                        >
                            Anular factura
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($isVoidModalVisible)
        <x-action-model
            title="Confirmar anulacion"
            :message="'Se anulara la factura ' . $invoice->number . '. Desea continuar?'"
            cancelMethod="cancelVoidAction"
            confirmMethod="confirmVoid"
            variant="danger"
        />
    @endif
</div>
