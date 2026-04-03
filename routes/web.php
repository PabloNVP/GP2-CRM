<?php

use App\Livewire\Clients\AddClient as AddClient;
use App\Livewire\Clients\IndexClient as ClientsIndex;
use App\Livewire\Clients\DeleteClient as DeleteClient;
use App\Livewire\Categories\AddCategory;
use App\Livewire\Categories\IndexCategory;
use App\Livewire\Orders\AddOrder as OrdersCreate;
use App\Livewire\Orders\IndexOrders as OrdersIndex;
use App\Livewire\Orders\ShowOrder as OrdersShow;
use App\Livewire\Invoices\IndexInvoices as InvoicesIndex;
use App\Livewire\Invoices\ShowInvoice as InvoicesShow;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\IndexUsers as AdminUsersIndex;
use App\Livewire\Products\AddProduct;
use App\Livewire\Products\IndexProducts as IndexProduct;
use App\Livewire\Tickets\AddTicket as TicketsCreate;
use App\Livewire\Tickets\IndexTickets as TicketsIndex;
use App\Livewire\Tickets\ShowTicket as TicketsShow;
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
    Route::middleware('role:administrador')->group(function () {
        Route::get('dashboard', AdminDashboard::class)
            ->name('dashboard');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('dashboard', AdminDashboard::class)
                ->name('dashboard');

            Route::get('users', AdminUsersIndex::class)
                ->name('users.index');
        });
    });

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

    Route::get('orders', OrdersIndex::class)
        ->name('orders.index');

    Route::get('orders/create', OrdersCreate::class)
        ->name('orders.create');

    Route::get('orders/{order}', OrdersShow::class)
        ->name('orders.show');

    Route::get('invoices', InvoicesIndex::class)
        ->name('invoices.index');

    Route::get('invoices/{invoice}', InvoicesShow::class)
        ->name('invoices.show');

    Route::get('tickets', TicketsIndex::class)
        ->name('tickets.index');

    Route::get('tickets/create', TicketsCreate::class)
        ->name('tickets.create');

    Route::get('tickets/{ticket}', TicketsShow::class)
        ->name('tickets.show');

    Route::view('profile', 'profile')
    //  ->middleware(['auth'])
        ->name('profile');
});

require __DIR__.'/auth.php';
