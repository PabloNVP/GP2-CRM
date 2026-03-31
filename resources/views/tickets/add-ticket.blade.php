<div>
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <a class="material-icons text-2xl filled-icon cursor-pointer" wire:navigate href="{{ route('tickets.index') }}">
                arrow_back
            </a>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">Agregar Ticket</h1>
                <p class="text-sm text-gray-500">Registra una nueva solicitud de soporte.</p>
            </div>
        </div>
    </div>

    <form method="POST" wire:submit.prevent="saveTicket" class="space-y-4 px-4 py-8 sm:px-6 lg:px-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                <label for="productId" class="block text-sm font-medium text-gray-700">Producto (opcional)</label>
                <select
                    id="productId"
                    wire:model.defer="productId"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                >
                    <option value="">Sin producto asociado</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('productId')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700">Prioridad</label>
                <select
                    id="priority"
                    wire:model.defer="priority"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                >
                    @foreach ($priorities as $priorityOption)
                        <option value="{{ $priorityOption->value }}">{{ ucfirst($priorityOption->value) }}</option>
                    @endforeach
                </select>
                @error('priority')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Asunto</label>
                <input
                    type="text"
                    id="subject"
                    wire:model.defer="subject"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700">Descripcion</label>
                <textarea
                    id="description"
                    wire:model.defer="description"
                    rows="4"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                ></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4">
                <p class="text-sm font-medium text-red-800">Error al guardar el ticket</p>
            </div>
        @endif

        <div class="flex justify-end">
            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-700"
            >
                Guardar ticket
            </button>
        </div>
    </form>
</div>
