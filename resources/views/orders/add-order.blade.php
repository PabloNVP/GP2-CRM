@php
use App\Enums\StateProductEnum;
@endphp

<div>
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <a class="material-icons text-2xl filled-icon cursor-pointer" wire:navigate href="{{ route('orders.index') }}">
                arrow_back
            </a>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">Agregar Orden</h1>
                <p class="text-sm text-gray-500">Registra una nueva orden con multiples productos.</p>
            </div>
        </div>
    </div>

    <form method="POST" wire:submit.prevent="saveOrder" class="space-y-6 px-4 py-8 sm:px-6 lg:px-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="clientId" class="block text-sm font-medium text-gray-700">Cliente</label>
                <select
                    id="clientId"
                    wire:model.defer="clientId"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                >
                    <option value="">Seleccionar cliente</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ trim($client->firstname.' '.$client->lastname) }}</option>
                    @endforeach
                </select>
                @error('clientId')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="date" class="block text-sm font-medium text-gray-700">Fecha</label>
                <input
                    type="date"
                    id="date"
                    wire:model.defer="date"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-1">
                <label for="observations" class="block text-sm font-medium text-gray-700">Observaciones</label>
                <input
                    type="text"
                    id="observations"
                    wire:model.defer="observations"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
                @error('observations')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">Items de la orden</h2>
                <button
                    type="button"
                    id="addItemButton"
                    wire:click="addItem"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700"
                >
                    Agregar item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Producto</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Cantidad</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Precio unitario</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Subtotal</th>
                            <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Accion</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($items as $index => $item)
                            <tr wire:key="order-item-{{ $index }}">
                                <td class="px-4 py-3 align-top">
                                    <select
                                        id="itemProduct-{{ $index }}"
                                        wire:model.live="items.{{ $index }}.product_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                        <option value="">Seleccionar producto</option>
                                        @foreach ($products as $product)
                                            <option
                                                value="{{ $product->id }}"
                                                @disabled($product->status === StateProductEnum::OUT_OF_STOCK)
                                            >
                                                {{ $product->name }}
                                                ({{ $product->status->value }})
                                                - ${{ number_format((float) $product->unit_price, 2, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('items.'.$index.'.product_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>

                                <td class="px-4 py-3 align-top">
                                    <input
                                        type="number"
                                        id="itemCount-{{ $index }}"
                                        min="1"
                                        wire:model.live="items.{{ $index }}.count"
                                        class="w-28 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                    @error('items.'.$index.'.count')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700 align-top">
                                    $ {{ number_format((float) ($item['unit_price'] ?? 0), 2, ',', '.') }}
                                </td>

                                <td class="px-4 py-3 text-sm font-medium text-gray-900 align-top">
                                    $ {{ number_format((float) ($item['subtotal'] ?? 0), 2, ',', '.') }}
                                </td>

                                <td class="px-4 py-3 text-center align-top">
                                    <button
                                        type="button"
                                        id="removeItem-{{ $index }}"
                                        wire:click="removeItem({{ $index }})"
                                        class="inline-flex items-center rounded-md border border-red-200 px-3 py-1 text-xs font-medium text-red-700 hover:bg-red-50"
                                    >
                                        Quitar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-200 flex justify-end">
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Total</p>
                    <p id="orderTotal" class="text-xl font-semibold text-gray-900">$ {{ number_format((float) $total, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4">
                <p class="text-sm font-medium text-red-800">Error al guardar la orden</p>
            </div>
        @endif

        <div class="flex justify-end">
            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-700"
            >
                Guardar orden
            </button>
        </div>
    </form>
</div>
