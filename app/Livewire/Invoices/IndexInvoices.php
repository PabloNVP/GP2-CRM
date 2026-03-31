<?php

namespace App\Livewire\Invoices;

use App\Enums\StateInvoiceEnum;
use App\Livewire\Actions\Invoices\ListingInvoice;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

final class IndexInvoices extends Component
{
    use WithPagination;

    public string $search = '';
    public string $stateFilter = '';
    public string $fromDate = '';
    public string $toDate = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStateFilter(): void
    {
        $this->resetPage();
    }

    public function updatedFromDate(): void
    {
        $this->resetPage();
    }

    public function updatedToDate(): void
    {
        $this->resetPage();
    }

    public function render(ListingInvoice $listingInvoice): View
    {
        return view('invoices.index-invoices', [
            'invoices' => $listingInvoice(
                $this->search,
                $this->stateFilter,
                $this->fromDate,
                $this->toDate,
            ),
            'states' => StateInvoiceEnum::cases(),
        ]);
    }
}
