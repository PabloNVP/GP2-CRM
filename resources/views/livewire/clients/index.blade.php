<div class="space-y-4">
    <h1 class="text-2xl font-semibold text-gray-800">Clientes</h1>

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
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <div class="p-4 text-center text-gray-500">No hay clientes registrados.</div>
            @endif
        </div>
    </div>
</div>
