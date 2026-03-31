<div class="flex flex-col gap-6 w-full">
    @php
        use App\Enums\PriorityTicketEnum;
        use App\Enums\RoleEnum;
        use App\Enums\StateTicketEnum;

        $user = auth()->user();
        $canManage = $user && in_array($user->role, [RoleEnum::SUPPORT, RoleEnum::ADMIN], true);
    @endphp

    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <span class="material-icons text-4xl filled-icon bg-primary text-white backdrop-blur-sm rounded-md p-2">
                confirmation_number
            </span>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">Tickets</h1>
                <p class="text-sm text-gray-500">Visualiza y prioriza solicitudes de soporte de clientes.</p>
            </div>
        </div>
        <span>
            <a
                href="{{ route('tickets.create') }}"
                wire:navigate
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
            >
                Agregar ticket
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
                placeholder="Buscar por asunto, cliente o numero de ticket..."
                class="pr-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            />
        </div>

        <div class="flex items-center gap-2">
            <h3 class="text-sm font-medium text-gray-700">Prioridad:</h3>
            <select
                wire:model.live.debounce.300ms="priorityFilter"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
                <option value="">Todas</option>
                @foreach ($priorities as $priority)
                    <option value="{{ $priority->value }}">{{ ucfirst($priority->value) }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <h3 class="text-sm font-medium text-gray-700">Estado:</h3>
            <select
                wire:model.live.debounce.300ms="stateFilter"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
                <option value="">Todos</option>
                @foreach ($states as $state)
                    <option value="{{ $state->value }}">{{ ucfirst(str_replace('_', ' ', $state->value)) }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <h3 class="text-sm font-medium text-gray-700">Producto:</h3>
            <select
                wire:model.live.debounce.300ms="productFilter"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
                <option value="">Todos</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <h3 class="text-sm font-medium text-gray-700">Desde:</h3>
            <input
                type="date"
                wire:model.live="fromDate"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            />
        </div>

        <div class="flex items-center gap-2">
            <h3 class="text-sm font-medium text-gray-700">Hasta:</h3>
            <input
                type="date"
                wire:model.live="toDate"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            />
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white mx-8">
        <div class="overflow-x-auto">
            @if (!$tickets->isEmpty())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Numero de ticket</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Asunto</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Cliente</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Producto</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Prioridad</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Fecha de creacion</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 text-center">
                                    {{ $ticket->id }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    {{ $ticket->subject }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    {{ trim(($ticket->client?->firstname ?? '').' '.($ticket->client?->lastname ?? '')) ?: '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    {{ $ticket->product?->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    @if ($canManage)
                                        <select
                                            wire:change="changePriority({{ $ticket->id }}, $event.target.value)"
                                            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                            @foreach ($priorities as $priorityOption)
                                                <option
                                                    value="{{ $priorityOption->value }}"
                                                    @selected($ticket->priority === $priorityOption)
                                                >
                                                    {{ ucfirst($priorityOption->value) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{ ucfirst($ticket->priority->value) }}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    <span @class([
                                        'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                        'bg-amber-100 text-amber-700' => $ticket->state === StateTicketEnum::OPEN,
                                        'bg-sky-100 text-sky-700' => $ticket->state === StateTicketEnum::IN_PROGRESS,
                                        'bg-emerald-100 text-emerald-700' => $ticket->state === StateTicketEnum::RESOLVED,
                                        'bg-slate-200 text-slate-700' => $ticket->state === StateTicketEnum::CLOSED,
                                    ])>
                                        {{ ucfirst(str_replace('_', ' ', $ticket->state->value)) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    {{ $ticket->created_at?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700 text-center">
                                    <a
                                        href="{{ route('tickets.show', $ticket) }}"
                                        wire:navigate
                                        class="buttonAction edit"
                                    >
                                        Ver detalle
                                    </a>

                                    @if ($canManage)
                                        @if ($ticket->state === StateTicketEnum::OPEN)
                                            <button
                                                type="button"
                                                wire:click='changeState({{ $ticket->id }}, @json(StateTicketEnum::IN_PROGRESS->value))'
                                                class="ml-2 buttonAction active"
                                            >
                                                En progreso
                                            </button>
                                        @endif

                                        @if ($ticket->state === StateTicketEnum::IN_PROGRESS)
                                            <button
                                                type="button"
                                                wire:click='changeState({{ $ticket->id }}, @json(StateTicketEnum::RESOLVED->value))'
                                                class="ml-2 buttonAction active"
                                            >
                                                Marcar resuelto
                                            </button>
                                        @endif

                                        @if ($ticket->state === StateTicketEnum::RESOLVED)
                                            <button
                                                type="button"
                                                wire:click='openStateModal({{ $ticket->id }}, @json(StateTicketEnum::CLOSED->value))'
                                                class="ml-2 buttonAction deactivate"
                                            >
                                                Cerrar
                                            </button>

                                            <button
                                                type="button"
                                                wire:click='openStateModal({{ $ticket->id }}, @json(StateTicketEnum::IN_PROGRESS->value))'
                                                class="ml-2 buttonAction active"
                                            >
                                                Reabrir
                                            </button>
                                        @endif

                                        @if ($ticket->state === StateTicketEnum::CLOSED)
                                            <button
                                                type="button"
                                                wire:click='openStateModal({{ $ticket->id }}, @json(StateTicketEnum::IN_PROGRESS->value))'
                                                class="ml-2 buttonAction active"
                                            >
                                                Reabrir
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($tickets->hasPages())
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                        {{ $tickets->links() }}
                    </div>
                @endif
            @else
                <div class="p-4 text-center text-gray-500">No hay tickets registrados</div>
            @endif
        </div>
    </div>

    @if ($isStateModalVisible)
        <x-action-model
            :title="$stateModalTitle"
            :message="$stateModalMessage"
            cancelMethod="cancelStateChange"
            confirmMethod="confirmStateChange"
            variant="danger"
        />
    @endif
</div>
