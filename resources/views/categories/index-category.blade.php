<div class="flex flex-col gap-6 w-full">
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <span class="material-icons text-4xl filled-icon bg-primary text-white backdrop-blur-sm rounded-md p-2">
                category
            </span>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">Categorias</h1>
                <p class="text-sm text-gray-500">Administra las categorias del catalogo de productos.</p>
            </div>
        </div>
        <span>
            <a
                href="{{ route('categories.create') }}"
                wire:navigate
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
            >
                Agregar categoria
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

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white mx-8">
        <div class="overflow-x-auto">
            @if (!$categories->isEmpty())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Nombre</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Descripcion</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Productos asociados</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($categories as $category)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 text-center">{{ $category->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">{{ $category->description ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">{{ $category->products_count }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-center">
                                    <a
                                        href="{{ route('categories.edit', $category) }}"
                                        wire:navigate
                                        class="inline-flex items-center rounded-md border border-indigo-200 px-3 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-50"
                                    >
                                        Editar
                                    </a>
                                    <button
                                        type="button"
                                        wire:click='openDeactivateModal({{ $category->id }}, @json($category->name))'
                                        class="ml-2 inline-flex items-center rounded-md border border-red-200 px-3 py-1 text-xs font-medium text-red-700 hover:bg-red-50"
                                    >
                                        Dar de baja
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($categories->hasPages())
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                        {{ $categories->links() }}
                    </div>
                @endif
            @else
                <div class="p-4 text-center text-gray-500">No hay categorias registradas.</div>
            @endif
        </div>
    </div>

    @if ($isDeactivateModalVisible)
        <livewire:categories.delete-category
            :categoryId="$categoryIdToDeactivate"
            :categoryName="$categoryNameToDeactivate"
        />
    @endif
</div>
