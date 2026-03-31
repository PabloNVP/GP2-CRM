<div class="flex flex-col gap-6 w-full">
    @php
        use App\Enums\RoleEnum;

        $user = auth()->user();
        $canRespond = $user && in_array($user->role, [RoleEnum::SUPPORT, RoleEnum::ADMIN], true);
    @endphp

    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <a class="material-icons text-2xl filled-icon cursor-pointer" wire:navigate href="{{ route('tickets.index') }}">
                arrow_back
            </a>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">Detalle de Ticket #{{ $ticket->id }}</h1>
                <p class="text-sm text-gray-500">Resumen del ticket registrado.</p>
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mx-8">
        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Cliente</p>
            <p class="text-sm font-medium text-gray-900">{{ trim(($ticket->client?->firstname ?? '').' '.($ticket->client?->lastname ?? '')) ?: '-' }}</p>
            <p class="mt-2 text-sm text-gray-600">{{ $ticket->client?->email ?? '-' }}</p>
            <p class="text-sm text-gray-600">{{ $ticket->client?->phone ?? '-' }}</p>
            <p class="text-sm text-gray-600">{{ $ticket->client?->company ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Asunto</p>
            <p class="text-sm font-medium text-gray-900">{{ $ticket->subject }}</p>
            <p class="mt-2 text-xs uppercase tracking-wide text-gray-500">Producto</p>
            <p class="text-sm text-gray-700">{{ $ticket->product?->name ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4">
            <p class="text-xs uppercase tracking-wide text-gray-500">Estado</p>
            <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $ticket->state->value)) }}</p>
            <p class="mt-2 text-xs uppercase tracking-wide text-gray-500">Prioridad</p>
            <p class="text-sm text-gray-700">{{ ucfirst($ticket->priority->value) }}</p>
            <p class="mt-2 text-xs uppercase tracking-wide text-gray-500">Fecha de creacion</p>
            <p class="text-sm text-gray-700">{{ $ticket->created_at?->format('d/m/Y H:i') ?? '-' }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white mx-8">
        <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-gray-500">Gestion del ticket</p>
                <p class="text-sm text-gray-600">Actualiza estado y prioridad segun el avance de atencion.</p>
            </div>

            @if ($canRespond)
                <div class="flex flex-wrap items-center gap-2">
                    <label for="ticket-priority" class="text-sm font-medium text-gray-700">Prioridad:</label>
                    <select
                        id="ticket-priority"
                        wire:change="changePriority($event.target.value)"
                        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                        @foreach (App\Enums\PriorityTicketEnum::cases() as $priorityOption)
                            <option
                                value="{{ $priorityOption->value }}"
                                @selected($ticket->priority === $priorityOption)
                            >
                                {{ ucfirst($priorityOption->value) }}
                            </option>
                        @endforeach
                    </select>

                    @if ($ticket->state === App\Enums\StateTicketEnum::OPEN)
                        <button
                            type="button"
                            wire:click='changeState(@json(App\Enums\StateTicketEnum::IN_PROGRESS->value))'
                            class="buttonAction active"
                        >
                            Pasar a En progreso
                        </button>
                    @endif

                    @if ($ticket->state === App\Enums\StateTicketEnum::IN_PROGRESS)
                        <button
                            type="button"
                            wire:click='changeState(@json(App\Enums\StateTicketEnum::RESOLVED->value))'
                            class="buttonAction active"
                        >
                            Marcar resuelto
                        </button>
                    @endif

                    @if ($ticket->state === App\Enums\StateTicketEnum::RESOLVED)
                        <button
                            type="button"
                            wire:click='openStateModal(@json(App\Enums\StateTicketEnum::CLOSED->value))'
                            class="buttonAction deactivate"
                        >
                            Cerrar ticket
                        </button>

                        <button
                            type="button"
                            wire:click='openStateModal(@json(App\Enums\StateTicketEnum::IN_PROGRESS->value))'
                            class="buttonAction active"
                        >
                            Reabrir ticket
                        </button>
                    @endif

                    @if ($ticket->state === App\Enums\StateTicketEnum::CLOSED)
                        <button
                            type="button"
                            wire:click='openStateModal(@json(App\Enums\StateTicketEnum::IN_PROGRESS->value))'
                            class="buttonAction active"
                        >
                            Reabrir ticket
                        </button>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-500">Solo usuarios de soporte o administracion pueden gestionar estado y prioridad.</p>
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white mx-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <p class="text-xs uppercase tracking-wide text-gray-500">Descripcion</p>
            <p class="text-sm text-gray-700 mt-2 whitespace-pre-line">{{ $ticket->description }}</p>
        </div>

        <div class="px-6 py-4 border-b border-gray-200">
            <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Agregar respuesta</p>

            @if ($canRespond)
                <form wire:submit.prevent="saveResponse" class="space-y-3">
                    <textarea
                        wire:model.defer="responseMessage"
                        rows="4"
                        placeholder="Escribe una respuesta para el cliente..."
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    ></textarea>

                    @error('responseMessage')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-700"
                        >
                            Guardar respuesta
                        </button>
                    </div>
                </form>
            @else
                <p class="text-sm text-gray-500">Solo usuarios de soporte o administracion pueden responder tickets.</p>
            @endif
        </div>

        <div class="px-6 py-4">
            <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Timeline de respuestas</p>
            @if ($ticket->responses->isEmpty())
                <p class="text-sm text-gray-500">Aun no hay respuestas registradas para este ticket.</p>
            @else
                <ul class="space-y-3">
                    @foreach ($ticket->responses as $response)
                        <li class="rounded-md border border-gray-200 p-3">
                            <p class="text-xs text-gray-500">{{ $response->user?->name ?? 'Usuario' }} - {{ $response->created_at?->format('d/m/Y H:i') }}</p>
                            <p class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $response->message }}</p>
                        </li>
                    @endforeach
                </ul>
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
