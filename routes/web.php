<?php

use App\Livewire\Clients\AddClient as AddClient;
use App\Livewire\Clients\IndexClient as ClientsIndex;
use App\Livewire\Clients\DeleteClient as DeleteClient;
use App\Livewire\Categories\AddCategory;
use App\Livewire\Categories\IndexCategory;
use App\Livewire\Products\AddProduct;
use App\Livewire\Products\IndexProducts as IndexProduct;
use Illuminate\Support\Facades\Route;

# Si esta logieado, redirige a home, sino a welcome
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

    Route::get('products', IndexProduct::class)
        ->name('products.index');

    Route::get('products/create', AddProduct::class)
        ->name('products.create');

    Route::get('products/{product}/edit', AddProduct::class)
        ->name('products.edit');

    Route::get('categories', IndexCategory::class)
        ->name('categories.index');

    Route::get('categories/create', AddCategory::class)
        ->name('categories.create');

    Route::get('categories/{category}/edit', AddCategory::class)
        ->name('categories.edit');

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
