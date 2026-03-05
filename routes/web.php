<?php

use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Clients\AddClient;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

/*Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'state', 'role:administrador'])
    ->name('dashboard');
*/

Route::middleware(['auth', 'verified', 'state'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware('role:administrador')
        ->name('dashboard');

    Route::get('clientes', ClientsIndex::class)
        ->name('clients.index');

    Route::get('clientes/create', AddClient::class)
        ->name('clients.create');

    Route::get('clientes/{client}/edit', AddClient::class)
        ->name('clients.edit');

    Route::view('profile', 'profile')
    //  ->middleware(['auth'])
        ->name('profile');
});

require __DIR__.'/auth.php';
