<div>
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <a class="material-icons text-2xl filled-icon cursor-pointer" wire:navigate href="{{ route('products.index') }}">
                arrow_back
            </a>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">
                    {{ $this->isEditing() ? 'Editar Producto' : 'Agregar Producto' }}
                </h1>
                <p class="text-sm text-gray-500">
                    {{ $this->isEditing() ? 'Modifica los datos del producto seleccionado.' : 'Registra un nuevo producto en el catálogo.' }}
                </p>
            </div>
        </div>
    </div>

    <form method="POST" wire:submit.prevent="saveProduct" class="space-y-4 px-4 py-8 sm:px-6 lg:px-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="categoryId" class="block text-sm font-medium text-gray-700">Categoría</label>
                <select
                    id="categoryId"
                    wire:model.defer="categoryId"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                >
                    <option value="">Seleccionar categoría</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                @error('categoryId')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input
                    type="text"
                    id="name"
                    wire:model.defer="name"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="unitPrice" class="block text-sm font-medium text-gray-700">Precio unitario</label>
                <input
                    type="number"
                    id="unitPrice"
                    wire:model.defer="unitPrice"
                    min="0"
                    step="0.01"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
                @error('unitPrice')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea
                    id="description"
                    wire:model.defer="description"
                    rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                ></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-15">
                {{ $this->isEditing() ? 'Guardar Cambios' : 'Agregar Producto' }}
            </button>
        </div>
    </form>
</div>
