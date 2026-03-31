@php
use App\Enums\StateOrderEnum;
use App\Enums\StateInvoiceEnum;
@endphp

<div class="flex flex-col gap-6 w-full">
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <span class="material-icons text-4xl filled-icon bg-primary text-white backdrop-blur-sm rounded-md p-2">
                shopping_cart
            </span>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">Ordenes</h1>
                <p class="text-sm text-gray-500">Visualiza el estado comercial y operativo de las ordenes.</p>
            </div>
        </div>
        <span>
            <a
                href="{{ route('orders.create') }}"
                wire:navigate
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
            >
                Agregar orden
            </a>
        </span>
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
                placeholder="Buscar por cliente o numero de orden..."
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
                @foreach ($stateOptions as $stateOption)
                    <option value="{{ $stateOption->value }}">{{ $stateOption->value }}</option>
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
            @if (!$orders->isEmpty())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Numero de orden</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Cliente</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Fecha</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 text-center">
                                    {{ $order->id }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    {{ trim(($order->client?->firstname ?? '').' '.($order->client?->lastname ?? '')) ?: '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    {{ $order->date?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-center">
                                    <span @class([
                                        'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                        'bg-amber-100 text-amber-700' => $order->state === StateOrderEnum::PENDING,
                                        'bg-sky-100 text-sky-700' => $order->state === StateOrderEnum::PROCESSING,
                                        'bg-blue-100 text-blue-700' => $order->state === StateOrderEnum::SHIPPED,
                                        'bg-emerald-100 text-emerald-700' => $order->state === StateOrderEnum::DELIVERED,
                                        'bg-red-100 text-red-700' => $order->state === StateOrderEnum::CANCELLED,
                                        'bg-violet-100 text-violet-700' => $order->state === StateOrderEnum::RETURNED,
                                    ])>
                                        {{ $order->state->value }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    $ {{ number_format((float) $order->total, 2, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-center">
                                    <a
                                        href="{{ route('orders.show', $order) }}"
                                        wire:navigate
                                        class="buttonAction edit"
                                    >
                                        Ver detalle
                                    </a>

                                    @if ($order->state === StateOrderEnum::PENDING)
                                        <button
                                            type="button"
                                            wire:click='changeState({{ $order->id }}, @json(StateOrderEnum::PROCESSING->value))'
                                            class="ml-2 buttonAction active"
                                        >
                                            Pasar a En proceso
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="openCancelModal({{ $order->id }})"
                                            class="ml-2 buttonAction deactivate"
                                        >
                                            Cancelar
                                        </button>
                                    @endif

                                    @if ($order->state === StateOrderEnum::PROCESSING)
                                        <button
                                            type="button"
                                            wire:click='changeState({{ $order->id }}, @json(StateOrderEnum::SHIPPED->value))'
                                            class="ml-2 buttonAction active"
                                        >
                                            Pasar a Enviado
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="openCancelModal({{ $order->id }})"
                                            class="ml-2 buttonAction deactivate"
                                        >
                                            Cancelar
                                        </button>
                                    @endif

                                    @if ($order->state === StateOrderEnum::SHIPPED)
                                        <button
                                            type="button"
                                            wire:click='changeState({{ $order->id }}, @json(StateOrderEnum::DELIVERED->value))'
                                            class="ml-2 buttonAction active"
                                        >
                                            Pasar a Entregado
                                        </button>
                                    @endif

                                    @if ($order->state === StateOrderEnum::DELIVERED && ! $order->invoice)
                                        <button
                                            type="button"
                                            wire:click="generateInvoice({{ $order->id }})"
                                            class="ml-2 buttonAction active"
                                        >
                                            Emitir factura
                                        </button>
                                    @endif

                                    @if ($order->invoice)
                                        <span @class([
                                            'ml-2 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                            'bg-sky-100 text-sky-700' => $order->invoice->state === StateInvoiceEnum::ISSUED,
                                            'bg-emerald-100 text-emerald-700' => $order->invoice->state === StateInvoiceEnum::PAID,
                                            'bg-red-100 text-red-700' => $order->invoice->state === StateInvoiceEnum::VOIDED,
                                        ])>
                                            Factura {{ $order->invoice->number }} - {{ $order->invoice->state->value }}
                                        </span>

                                        <a
                                            href="{{ route('invoices.show', $order->invoice) }}"
                                            wire:navigate
                                            class="ml-2 buttonAction edit"
                                        >
                                            Ver factura
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($orders->hasPages())
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                        {{ $orders->links() }}
                    </div>
                @endif
            @else
                <div class="p-4 text-center text-gray-500">No hay ordenes registradas</div>
            @endif
        </div>
    </div>

    @if ($isCancelModalVisible)
        <x-action-model
            title="Confirmar cancelacion"
            :message="'Se cancelara la orden #' . $orderIdToCancel . '. Desea continuar?'"
            cancelMethod="cancelCancelAction"
            confirmMethod="confirmCancel"
            variant="danger"
        />
    @endif
</div>
