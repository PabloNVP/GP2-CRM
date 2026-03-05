<div class="flex flex-col gap-4 space-y-12 px-4 py-5 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-800">Clientes</h1>

    @if (session('message'))
        <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <input
        type="text"
        wire:model.live.debounce.300ms="search"
        placeholder="Buscar por nombre o email..."
        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
    />

    <select
        wire:model.live.debounce.300ms="stateFilter" 
        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Todos los estados</option>
        <option value="{{ \App\Enums\StateEnum::ACTIVE->value }}">Activo</option>
        <option value="{{ \App\Enums\StateEnum::INACTIVE->value }}">Inactivo</option>
    </select>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
        <div class="overflow-x-auto">
            @if (!$clients->isEmpty())
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Teléfono</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Empresa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach ($clients as $client)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                {{ trim(($client->firstname ?? '').' '.($client->lastname ?? '')) }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $client->email }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $client->phone }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $client->company }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $client->state }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                <a
                                    href="{{ route('clients.edit', $client) }}"
                                    wire:navigate
                                    class="font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    Editar
                                </a>
                                <button
                                    type="button"
                                    wire:click="confirmDeactivate({{ $client->id }})"
                                    class="ml-3 font-medium text-red-600 hover:text-red-800"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="p-4 text-center text-gray-500">No hay clientes registrados.</div>
            @endif
        </div>
    </div>

    @if ($confirmingDeletion)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900/60 px-4">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-gray-900">Confirmar eliminacion</h2>
                <p class="mt-2 text-sm text-gray-600">
                    {{ 'Se dara de baja al cliente '.($clientToDeactivateName !== '' ? $clientToDeactivateName : 'seleccionado').'. Esta accion lo marcara como inactivo.' }}
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        wire:click="cancelDeactivate"
                        class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Cancelar
                    </button>

                    <button
                        type="button"
                        wire:click="deactivateClient"
                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                    >
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
