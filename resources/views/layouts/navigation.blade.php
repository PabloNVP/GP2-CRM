<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

@php
    $isDashboard = request()->routeIs('dashboard');
    $isClients = request()->routeIs('clients.*');
    $isProfile = request()->routeIs('profile');
    $isProducts = request()->routeIs('products.*');
    $isCategories = request()->routeIs('categories.*');

    $navItemBase = 'flex items-center gap-3 px-3 py-2 rounded-lg transition-colors';
    $navItemActive = 'bg-primary/10 text-primary font-semibold';
    $navItemIdle = 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800';
@endphp

<aside class="w-64 flex-shrink-0 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col">

<div class="p-6 flex items-center gap-3">
    <div class="size-10 rounded-lg bg-primary flex items-center justify-center text-white p-2">
        <span class="material-icons">rocket_launch</span>
    </div>
    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">CRM Pro</h1>
</div>

<nav class="flex-1 px-4 space-y-1">
    <a
        href="{{ route('dashboard') }}"
        wire:navigate
        class="{{ $navItemBase }} {{ $isDashboard ? $navItemActive : $navItemIdle }}"
        @if ($isDashboard) aria-current="page" @endif
    >
        <span class="material-icons">dashboard</span>
        <span>Dashboard</span>
    </a>

    <a
        href="{{ route('clients.index') }}"
        wire:navigate
        class="{{ $navItemBase }} {{ $isClients ? $navItemActive : $navItemIdle }}"
        @if ($isClients) aria-current="page" @endif
    >
        <span class="material-icons">group</span>
        <span>Clientes</span>
    </a>

    <a 
        href="{{ route('products.index') }}"
        wire:navigate
        class="{{ $navItemBase }} {{ $isProducts ? $navItemActive : $navItemIdle }}"
        @if ($isProducts) aria-current="page" @endif
    >
        <span class="material-icons">inventory_2</span>
        <span>Productos</span>
    </a>

    @if ($isProducts || $isCategories)
    <div class="ml-5 border-l border-slate-200 dark:border-slate-700 pl-3">
        <a
            href="{{ route('categories.index') }}"
            wire:navigate
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ $isCategories ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            @if ($isCategories) aria-current="page" @endif
        >
            <span class="material-icons text-[18px]">subdirectory_arrow_right</span>
            <span>Categorías</span>
        </a>
    </div>
    @endif

    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="#">
        <span class="material-icons">shopping_cart</span>
        <span>Pedidos</span>
    </a>

    <a class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="#">
        <span class="material-icons">confirmation_number</span>
        <span>Tickets</span>
    </a>
    
</nav>

<div class="p-4 border-t border-slate-200 dark:border-slate-800">
    <button class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-bold hover:bg-primary/90 transition-all">
        <span class="material-icons text-sm">add_chart</span>
        <span>New Report</span>
    </button>
</div>

<div class="p-4 flex items-center gap-3">
    <a href="{{ route('profile') }}" wire:navigate class="block">
        <div
            class="size-10 rounded-full bg-slate-200 dark:bg-slate-700 bg-cover bg-center cursor-pointer border-2 transition-colors {{ $isProfile ? 'border-primary' : 'border-transparent' }}"
            data-alt="User profile avatar of a smiling man"
            style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDdB1bXD6zB1LutKuQq4UIPwk0E9VX4F8jnRm64_M-oYyej2WpTWw4Ig2svN5j0Hx-VDt30Py8jCFPuG62wvz8QQZ6kmvSQycjP8OR-iqTY3z_ZT43RCZyDj4KLMpmSDFteAfICqlJXZRFbMJtt_sQDBa4s9kCqWIaVvBz_hMRp94_DgpI3isBcx53BgdIl1ngiuWPG6KREpiQsOI0nTf-4urHFfL3uPo1eOdlvZgLWxLZYUXWFpq80z7zVF0W6dqmj8tNA4Nyktfg')"
        ></div>
    </a>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-bold truncate">
            {{ auth()->user()->name }}
        </p>
        <p class="text-xs text-slate-500 truncate">
            {{ auth()->user()->role }}
        </p>
    </div>
    
    <span class="material-icons text-slate-400 cursor-pointer" wire:click="logout">
        exit_to_app
    </span>
</div>

</aside>
