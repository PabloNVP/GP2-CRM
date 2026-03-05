<?php

use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Clients\AddClient;
use Illuminate\Support\Facades\Route;

# Si esta logieado, redirige a dashboard, sino a welcome
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

/*Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'state', 'role:administrador'])
    ->name('dashboard');
*/

Route::middleware(['auth', 'verified', 'state'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware('role:administrador')
        ->name('dashboard');

    Route::get('clients', ClientsIndex::class)
        ->name('clients.index');

    Route::get('clients/create', AddClient::class)
        ->name('clients.create');

    Route::get('clients/{client}/edit', AddClient::class)
        ->name('clients.edit');

    Route::view('profile', 'profile')
    //  ->middleware(['auth'])
        ->name('profile');
});

require __DIR__.'/auth.php';
