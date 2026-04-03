<div class="flex flex-col gap-6 w-full">
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <span class="material-icons text-4xl filled-icon bg-primary text-white backdrop-blur-sm rounded-md p-2">
                admin_panel_settings
            </span>
            <div class="flex flex-col gap-1">
                <h1 class="text-4xl font-semibold text-gray-800">Dashboard de administracion</h1>
                <p class="text-sm text-gray-500">Resumen de usuarios del sistema y distribucion por rol.</p>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-4 mx-8">
        <div class="flex-1 rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500">Total usuarios</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $totalUsers }}</p>
        </div>

        <div class="flex-1 rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500">Usuarios activos</p>
            <p class="mt-2 text-3xl font-semibold text-emerald-700">{{ $activeUsers }}</p>
        </div>

        <div class="flex-1 rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500">Usuarios inactivos</p>
            <p class="mt-2 text-3xl font-semibold text-red-700">{{ $inactiveUsers }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white mx-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Distribucion por Rol</h2>
        </div>

        @if ($totalUsers > 0)
            <div class="divide-y divide-gray-200">
                @foreach ($rolesDistribution as $item)
                    <div class="flex items-center justify-between px-6 py-3">
                        <span class="text-sm text-gray-700">{{ ucfirst($item['role']->value) }}</span>
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                            {{ $item['count'] }} usuario(s)
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-4 text-center text-gray-500">No hay usuarios registrados.</div>
        @endif
    </div>
</div>
