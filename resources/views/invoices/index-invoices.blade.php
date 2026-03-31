@php
use App\Enums\StateInvoiceEnum;
@endphp

<div class="flex flex-col gap-6 w-full">
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <span class="material-icons text-4xl filled-icon bg-primary text-white backdrop-blur-sm rounded-md p-2">
                receipt_long
            </span>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">Facturas</h1>
                <p class="text-sm text-gray-500">Consulta las facturas emitidas y aplica filtros por estado o fecha.</p>
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

    <div class="flex items-center justify-between mx-16 gap-4 flex-wrap">
        <div class="flex items-center gap-2 relative">
            <span class="material-icons">search</span>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Buscar por cliente o numero de factura..."
                class="pr-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            />
        </div>

        <div class="flex items-center gap-2">
            <h3 class="text-sm font-medium text-gray-700">Estado:</h3>
            <select
                wire:model.live.debounce.300ms="stateFilter"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
                <option value="">Todos</option>
                @foreach ($states as $state)
                    <option value="{{ $state->value }}">{{ $state->value }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <h3 class="text-sm font-medium text-gray-700">Desde:</h3>
            <input
                type="date"
                wire:model.live="fromDate"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            />
        </div>

        <div class="flex items-center gap-2">
            <h3 class="text-sm font-medium text-gray-700">Hasta:</h3>
            <input
                type="date"
                wire:model.live="toDate"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            />
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white mx-8">
        <div class="overflow-x-auto">
            @if (!$invoices->isEmpty())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Numero de factura</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Orden</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Cliente</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Fecha de emision</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 text-center">
                                    {{ $invoice->number }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    #{{ $invoice->order_id }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    {{ trim(($invoice->order?->client?->firstname ?? '').' '.($invoice->order?->client?->lastname ?? '')) ?: '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    {{ $invoice->issue_date?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-center">
                                    <span @class([
                                        'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                        'bg-sky-100 text-sky-700' => $invoice->state === StateInvoiceEnum::ISSUED,
                                        'bg-emerald-100 text-emerald-700' => $invoice->state === StateInvoiceEnum::PAID,
                                        'bg-red-100 text-red-700' => $invoice->state === StateInvoiceEnum::VOIDED,
                                    ])>
                                        {{ $invoice->state->value }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    $ {{ number_format((float) $invoice->total_amount, 2, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-center">
                                    <a
                                        href="{{ route('invoices.show', $invoice) }}"
                                        wire:navigate
                                        class="buttonAction edit"
                                    >
                                        Ver detalle
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($invoices->hasPages())
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                        {{ $invoices->links() }}
                    </div>
                @endif
            @else
                <div class="p-4 text-center text-gray-500">No hay facturas registradas</div>
            @endif
        </div>
    </div>
</div>
