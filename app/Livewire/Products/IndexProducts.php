<?php

namespace App\Livewire\Products;

use App\Enums\StateProductEnum;
use App\Livewire\Actions\Products\ListeringProduct;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class IndexProducts extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $categoryFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function render(ListeringProduct $listeringProduct)
    {
        $products = $listeringProduct(
            search: $this->search,
            statusFilter: $this->statusFilter,
            categoryFilter: $this->categoryFilter,
        );

        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $statusOptions = StateProductEnum::cases();

        return view('products.index-products', compact('products', 'categories', 'statusOptions'));
    }
}
