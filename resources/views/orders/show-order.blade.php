<div class="flex flex-col gap-6 w-full">
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <a class="material-icons text-2xl filled-icon cursor-pointer" wire:navigate href="{{ route('orders.index') }}">
                arrow_back
            </a>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">Detalle de Orden #{{ $order->id }}</h1>
                <p class="text-sm text-gray-500">Resumen de la orden creada.</p>
            </div>
        </div>
    </div>

    @if (session('message'))
        <div class="rounded-md border border-green-200 bg-green-50 px-6 py-4 mx-8 text-sm text-green-700">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mx-8">
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Cliente</p>
            <p class="text-sm font-medium text-gray-900">{{ trim(($order->client?->firstname ?? '').' '.($order->client?->lastname ?? '')) ?: '-' }}</p>
            <p class="mt-2 text-sm text-gray-600">{{ $order->client?->email ?? '-' }}</p>
            <p class="text-sm text-gray-600">{{ $order->client?->phone ?? '-' }}</p>
            <p class="text-sm text-gray-600">{{ $order->client?->company ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Fecha</p>
            <p class="text-sm font-medium text-gray-900">{{ $order->date?->format('d/m/Y') ?? '-' }}</p>
            <p class="mt-2 text-xs uppercase tracking-wide text-gray-500">Observaciones</p>
            <p class="text-sm text-gray-600">{{ $order->observations ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Estado</p>
            <p class="text-sm font-medium text-gray-900">{{ $order->state->value }}</p>
            <p class="mt-2 text-xs uppercase tracking-wide text-gray-500">Total</p>
            <p class="text-sm font-medium text-gray-900">$ {{ number_format((float) $order->total, 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white mx-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Precio unitario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($order->details as $detail)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $detail->product?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $detail->count }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">$ {{ number_format((float) $detail->unit_price, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">$ {{ number_format((float) $detail->subtotal, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-sm text-center text-gray-500">La orden no tiene items cargados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-200 px-6 py-4 text-right">
            <p class="text-xs uppercase tracking-wide text-gray-500">Total</p>
            <p class="text-xl font-semibold text-gray-900">$ {{ number_format((float) $order->total, 2, ',', '.') }}</p>
        </div>
    </div>
</div>
