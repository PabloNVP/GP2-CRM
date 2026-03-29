@php
use App\Enums\StateProductEnum;
@endphp

<div class="flex flex-col gap-6 w-full">
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <span class="material-icons text-4xl filled-icon bg-primary text-white backdrop-blur-sm rounded-md p-2">
                inventory
            </span>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">Productos</h1>
                <p class="text-sm text-gray-500">Listado general del catalogo de productos.</p>
            </div>
        </div>
        <span>
            <a
                href="{{ route('products.create') }}"
                wire:navigate
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
            >
                Agregar producto
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
                placeholder="Buscar por nombre..."
                class="pr-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            />
        </div>

        <div class="flex items-center gap-2">
            <h3 class="text-sm font-medium text-gray-700">Categoria:</h3>
            <select
                wire:model.live.debounce.300ms="categoryFilter"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
                <option value="">Todas</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <h3 class="text-sm font-medium text-gray-700">Estado:</h3>
            <select
                wire:model.live.debounce.300ms="statusFilter"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
                <option value="">Todos</option>
                @foreach ($statusOptions as $statusOption)
                    <option value="{{ $statusOption->value }}">{{ $statusOption->value }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white mx-8">
        <div class="overflow-x-auto">
            @if (!$products->isEmpty())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Nombre</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Categoria</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Precio Unitario</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">                   
                        @foreach ($products as $product)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 text-center">
                                    {{ $product->name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    {{ $product->category?->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    $ {{ $product->unit_price ?? 0.00 }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-center">
                                    <span class="state {{ $product->status === StateProductEnum::AVAILABLE ? 'active' :
                                    ($product->status === StateProductEnum::OUT_OF_STOCK ? 'out' : 'inactive') }}">
                                        {{ ucfirst($product->status->value) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-center">
                                    <a
                                        href="{{ route('products.edit', $product) }}"
                                        wire:navigate
                                        class="buttonAction edit"
                                    >
                                        Editar
                                    </a>

                                    @if ($product->status === StateProductEnum::OUT_OF_STOCK || $product->status === StateProductEnum::DISCONTINUED)
                                    <button
                                        type="button"
                                        wire:click='openDeactivateModal({{ $product->id }}, @json($product->name), "activate")'
                                        class="ml-2 buttonAction active"
                                    >
                                        Activar
                                    </button>
                                    @endif

                                    @if ($product->status === StateProductEnum::AVAILABLE || $product->status === StateProductEnum::OUT_OF_STOCK)
                                    <button
                                        type="button"
                                        wire:click='openDeactivateModal({{ $product->id }}, @json($product->name), "deactivate")'
                                        class="ml-2 buttonAction deactivate"
                                    >
                                        Desactivar
                                    </button>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($products->hasPages())
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $products->links() }}
                </div>
                @endif
            @else
                <div class="p-4 text-center text-gray-500">No hay productos registrados</div>
            @endif
        </div>
    </div>

    @if ($isDeactivateModalVisible)
        <x-action-model
            :title="$productActionType === 'activate' ? 'Confirmar activacion' : 'Confirmar desactivacion'"
            :message="'Se ' . ($productActionType === 'activate' ? 'activara' : 'desactivara') . ' el producto ' . $productNameToAction . '. Desea continuar?'"
            cancelMethod="cancelAction"
            confirmMethod="confirmAction"
            :variant="$productActionType === 'activate' ? 'success' : 'danger'"
        />
    @endif
</div>
