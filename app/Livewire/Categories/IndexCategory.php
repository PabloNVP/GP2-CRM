<?php

namespace App\Livewire\Categories;

use App\Livewire\Actions\Categories\ListeringCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class IndexCategory extends Component
{
    use WithPagination;

    public bool $isDeactivateModalVisible = false;
    public ?int $categoryIdToDeactivate = null;
    public ?string $categoryNameToDeactivate = null;

    public function openDeactivateModal(int $categoryId, string $categoryName): void
    {
        $this->categoryIdToDeactivate = $categoryId;
        $this->categoryNameToDeactivate = $categoryName;
        $this->isDeactivateModalVisible = true;
    }

    #[On('close-deactivate-modal')]
    public function closeDeactivateModal(): void
    {
        $this->isDeactivateModalVisible = false;
    }

    #[on('show-message')]
    public function showMessage(string $message) : void
    {
        session()->flash('message', $message);
    }

    #[on('show-error')]
    public function showError(string $message) : void
    {
        session()->flash('error', $message);
    }

    public function render(ListeringCategory $listeringCategory)
    {
        $categories = $listeringCategory();

        return view('categories.index-category', compact('categories'));
    }

}
