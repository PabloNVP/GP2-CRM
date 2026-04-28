@php
    $isInfo = $tab === 'info';
    $isPassword = $tab === 'password';
    $isDelete = $tab === 'delete';
@endphp

<div>
    <div class="flex items-center justify-between bg-white border-r border-slate-200 gap-4 flex-wrap mb-4 px-4 py-4 sm:px-6 lg:px-12">
        <div class="flex items-center gap-4">
            <div class="flex gap-1">
                <button
                    type="button"
                    wire:click="setTab('info')"
                    class="px-3 py-2 rounded-md text-sm font-medium {{ $isInfo ? 'bg-primary/10 text-primary' : 'text-gray-500 hover:bg-gray-100' }}"
                >
                    {{ __('Profile Information') }}
                </button>
                <button
                    type="button"
                    wire:click="setTab('password')"
                    class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ $isPassword ? 'bg-primary/10 text-primary' : 'text-gray-500 hover:bg-gray-100' }}"
                >
                    {{ __('Change Password') }}
                </button>
                <button
                    type="button"
                    wire:click="setTab('delete')"
                    class="ml-4 px-3 py-2 rounded-md text-sm font-medium {{ $isDelete ? 'bg-primary/10 text-primary' : 'text-gray-500 hover:bg-gray-100' }}"
                >
                    {{ __('Delete Account') }}
                </button>
            </div>
        </div>
    </div>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 space-y-6">
            @if ($isInfo)
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>
            @elseif ($isPassword)
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>
            @elseif ($isDelete)
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
