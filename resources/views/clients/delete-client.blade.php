
<div class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900/60 px-4">
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
        <h2 class="text-lg font-semibold text-gray-900">Confirmar eliminacion</h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ 'Se dara de baja al cliente '.($clientName !== '' ? 
                $clientName : 
                'seleccionado').'. 
                Esta accion lo marcara como inactivo.' }}
        </p>

        <div class="mt-6 flex justify-end gap-3">
            <button
                type="button"
                wire:click="cancelUpgrade"
                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Cancelar
            </button>

            <button
                type="button"
                wire:click="upgradeClient"
                class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
            >
                Confirmar
            </button>
        </div>
    </div>
</div>