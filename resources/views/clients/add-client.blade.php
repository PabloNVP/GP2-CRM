
<div>
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">   
        <div class="flex items-center gap-4"> 
            <a class="material-icons text-2xl filled-icon cursor-pointer" wire:navigate href="{{ route('clients.index') }}">
                arrow_back
            </a>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">
                    {{ $this->isEditing() ? 'Editar Cliente' : 'Agregar Cliente' }}
                </h1>
                <p class="text-sm text-gray-500">
                    {{ $this->isEditing() ? 'Edita la información del cliente.' : 'Agrega un nuevo cliente al sistema.' }}
                </p>
            </div>
        </div>
    </div>


    <form method="POST" wire:submit.prevent="saveClient" class="space-y-4 px-4 py-8 sm:px-6 lg:px-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="firstname" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input
                    type="text"
                    id="firstname"
                    wire:model.defer="firstname"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
            </div>

            <div>
                <label for="lastname" class="block text-sm font-medium text-gray-700">Apellido</label>
                <input
                    type="text"
                    id="lastname"
                    wire:model.defer="lastname"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input
                    type="email"
                    id="email"
                    wire:model.defer="email"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input
                    type="text"
                    id="phone"
                    wire:model.defer="phone"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Dirección</label>
                <input
                    type="text"
                    id="address"
                    wire:model.defer="address"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
            </div>

            <div>
                <label for="company" class="block text-sm font-medium text-gray-700">Empresa</label>
                <input
                    type="text"
                    id="company"
                    wire:model.defer="company"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
            </div>
        </div>

        <div>
            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.75 7.25a.75.75 0 011.5 0v3.5a.75.75 0 11-1.5 0v-3.5zM9.25 14a.75.75 0 111.5-.75v-.25a.75.75 0 11-1.5-.75v-.25a.75.75 0 111.5-.75v-.25a.75.75 0 11-1.5-.75v-.25z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Error al guardar el cliente</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-4 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-15">
                {{ $this->isEditing() ? 'Guardar Cambios' : 'Agregar Cliente' }}
            </button>
        </div>
    </form>
</div>
