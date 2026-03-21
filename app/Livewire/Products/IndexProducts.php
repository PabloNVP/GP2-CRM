<?php

namespace App\Livewire\Products;

use App\Enums\StateProductEnum;
use App\Livewire\Actions\Products\DeactivateProduct;
use App\Livewire\Actions\Products\ListeringProduct;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;
use Livewire\WithPagination;

class IndexProducts extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = StateProductEnum::AVAILABLE->value;
    public string $categoryFilter = '';
    public bool $isDeactivateModalVisible = false;
    public ?int $productIdToDeactivate = null;
    public string $productNameToDeactivate = '';

    public function openDeactivateModal(int $productId, string $productName): void
    {
        $this->productIdToDeactivate = $productId;
        $this->productNameToDeactivate = $productName;
        $this->isDeactivateModalVisible = true;
    }

    public function cancelDeactivate(): void
    {
        $this->resetDeactivateState();
    }

    public function confirmDeactivate(DeactivateProduct $deactivateProduct): void
    {
        if (! $this->productIdToDeactivate) {
            session()->flash('error', 'No se selecciono ningun producto.');
            $this->resetDeactivateState();

            return;
        }

        try {
            $deactivateProduct($this->productIdToDeactivate);

            session()->flash('message', 'Producto actualizado de estado correctamente.');
        } catch (ModelNotFoundException) {
            session()->flash('error', 'El producto seleccionado no existe.');
        }

        $this->resetDeactivateState();
    }

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

    private function resetDeactivateState(): void
    {
        $this->isDeactivateModalVisible = false;
        $this->productIdToDeactivate = null;
        $this->productNameToDeactivate = '';
    }
}
