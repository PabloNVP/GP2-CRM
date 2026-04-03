@php
use App\Enums\StateEnum;
@endphp

<div class="flex flex-col gap-6 w-full">
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <a class="material-icons text-2xl filled-icon cursor-pointer" wire:navigate href="{{ route('admin.dashboard') }}">
                arrow_back
            </a>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">Usuarios</h1>
                <p class="text-sm text-gray-500">Gestiona accesos y perfiles del sistema.</p>
            </div>
        </div>
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
                id="admin-users-search"
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Buscar por nombre o email..."
                class="pr-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            />
        </div>

        <div wire:loading wire:target="search, roleFilter, stateFilter">
            <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div class="flex items-center gap-2 relative">
            <h3 class="text-sm font-medium text-gray-700">Rol:</h3>
            <select
                wire:model.live.debounce.300ms="roleFilter"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
                <option value="">Todos</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}">{{ ucfirst($role->value) }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2 relative">
            <h3 class="text-sm font-medium text-gray-700">Estado:</h3>
            <select
                wire:model.live.debounce.300ms="stateFilter"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
                <option value="">Todos</option>
                @foreach ($states as $state)
                    <option value="{{ $state->value }}">{{ ucfirst($state->value) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white mx-8">
        <div class="overflow-x-auto">
            @if (!$users->isEmpty())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Nombre</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Rol</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Fecha de alta</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($users as $user)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 text-center">{{ $user->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">{{ $user->email }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">{{ ucfirst($user->role->value) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-center">
                                    <span @class([
                                        'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                        'bg-emerald-100 text-emerald-700' => $user->state === StateEnum::ACTIVE,
                                        'bg-red-100 text-red-700' => $user->state === StateEnum::INACTIVE,
                                    ])>
                                        {{ ucfirst($user->state->value) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">{{ $user->created_at?->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-center">
                                    <label for="role-user-{{ $user->id }}" class="sr-only">Cambiar rol</label>
                                    <select
                                        id="role-user-{{ $user->id }}"
                                        wire:change="openRoleModal({{ $user->id }}, $event.target.value)"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    >
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->value }}" @selected($user->role === $role)>
                                                {{ ucfirst($role->value) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @if ($user->state === StateEnum::ACTIVE)
                                        <button
                                            id="state-action-user-{{ $user->id }}"
                                            type="button"
                                            wire:click="openStateModal({{ $user->id }}, '{{ StateEnum::INACTIVE->value }}')"
                                            class="ml-3 buttonAction deactivate"
                                        >
                                            Desactivar
                                        </button>
                                    @else
                                        <button
                                            id="state-action-user-{{ $user->id }}"
                                            type="button"
                                            wire:click="openStateModal({{ $user->id }}, '{{ StateEnum::ACTIVE->value }}')"
                                            class="ml-3 buttonAction active"
                                        >
                                            Activar
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($users->hasPages())
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                @endif
            @else
                <div class="p-4 text-center text-gray-500">No hay usuarios registrados.</div>
            @endif
        </div>
    </div>

    @if ($isRoleModalVisible)
        <x-action-model
            :title="$roleModalTitle"
            :message="$roleModalMessage"
            cancelMethod="cancelRoleChange"
            confirmMethod="confirmRoleChange"
            variant="danger"
        />
    @endif

    @if ($isStateModalVisible)
        <x-action-model
            :title="$stateModalTitle"
            :message="$stateModalMessage"
            cancelMethod="cancelStateChange"
            confirmMethod="confirmStateChange"
            :variant="$pendingStateValue === StateEnum::ACTIVE->value ? 'success' : 'danger'"
        />
    @endif
</div>
