<?php

use App\Livewire\Clients\Index as ClientsIndex;
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

    Route::view('profile', 'profile')
    //  ->middleware(['auth'])
        ->name('profile');
});

require __DIR__.'/auth.php';
