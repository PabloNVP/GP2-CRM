@props([
    'title' => 'Confirmar accion',
    'message' => 'Desea continuar con esta accion?',
    'confirmMethod' => 'confirmAction',
    'cancelMethod' => 'cancelAction',
    'confirmText' => 'Confirmar',
    'cancelText' => 'Cancelar',
    'variant' => 'danger', // 'danger' | 'success'
])

@php
    $safeConfirmMethod = preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $confirmMethod) ? $confirmMethod : 'confirmAction';
    $safeCancelMethod = preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $cancelMethod) ? $cancelMethod : 'cancelAction';

    $confirmButtonClass = $variant === 'success'
        ? 'rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:opacity-60'
        : 'rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60';
@endphp

<div
    class="fixed inset-0 z-40 flex items-center justify-center bg-gray-900/60 px-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="action-modal-title"
>
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
        <h2 id="action-modal-title" class="text-lg font-semibold text-gray-900">
            {{ $title }}
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ $message }}
        </p>

        <div class="mt-6 flex justify-end gap-3">
            <button
                type="button"
                wire:click="{{ $safeCancelMethod }}"
                wire:loading.attr="disabled"
                wire:target="{{ $safeCancelMethod }},{{ $safeConfirmMethod }}"
                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60"
            >
                {{ $cancelText }}
            </button>

            <button
                type="button"
                wire:click="{{ $safeConfirmMethod }}"
                wire:loading.attr="disabled"
                wire:target="{{ $safeConfirmMethod }}"
                class="{{ $confirmButtonClass }}"
            >
                {{ $confirmText }}
            </button>
        </div>
    </div>
</div>