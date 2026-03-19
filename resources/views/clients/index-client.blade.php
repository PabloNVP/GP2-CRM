@php
use \App\Enums\StateEnum;
@endphp

<div class="flex flex-col gap-6 w-full">
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">   
        <div class="flex items-center gap-4"> 
            <span class="material-icons text-4xl filled-icon bg-primary text-white backdrop-blur-sm rounded-md p-2">
                person
            </span>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">
                    Clientes
                </h1>
                <p class="text-sm text-gray-500">
                    Administra tus clientes, edítalos o elimínalos.
                </p>
            </div>
        </div>
        <span>
            <a
                href="{{ route('clients.create') }}"
                wire:navigate
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
            >
                Agregar cliente
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

    <div class="flex items-center justify-between mx-16">
        <div class="flex items-center gap-2 relative">
            <span class="material-icons">
                search
            </span>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Buscar por nombre o email..."
                class="pr-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            />
        </div>

        <div wire:loading wire:target="stateFilter" >
            <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        
        <div class="flex items-center gap-2 relative">
            <h3 class="text-sm font-medium text-gray-700">Estado: </h3>
            <select
                wire:model.live.debounce.300ms="stateFilter" 
                wire:loading.attr="disabled"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Todos los estados</option>
                <option value="{{ StateEnum::ACTIVE->value }}">Activo</option>
                <option value="{{ StateEnum::INACTIVE->value }}">Inactivo</option>
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white mx-8">
        <div class="overflow-x-auto">
            @if (!$clients->isEmpty())
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Teléfono</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Empresa</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach ($clients as $client)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 text-center">
                                {{ trim(($client->firstname ?? '').' '.($client->lastname ?? '')) }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">{{ $client->email }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">{{ $client->phone }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">{{ $client->company }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-center">
                                <label class="state {{ $client->state === StateEnum::ACTIVE ? 'active' : 'inactive' }}">
                                    {{ $client->state }}
                                </label>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                <a
                                    href="{{ route('clients.edit', $client) }}"
                                    wire:navigate
                                    class="font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    Editar
                                </a>
                                <a
                                    href="#"
                                    wire:click.prevent="$set('isVisible', true)"
                                    class="ml-3 font-medium text-red-600 hover:text-red-800"
                                >
                                    @if ($client->state === StateEnum::ACTIVE)
                                        Eliminar
                                    @else
                                        Reactivar
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($clients->hasPages())
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                {{ $clients->links() }}
            </div>
            @endif
            
            @else
                <div class="p-4 text-center text-gray-500">No hay clientes registrados.</div>
            @endif
        </div>
        
    </div>
 
    @if($isVisible)
        <livewire:clients.delete-client :client="$client" />
    @endif
</div>
