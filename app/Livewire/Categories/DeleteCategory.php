<?php

namespace App\Livewire\Categories;

use App\Livewire\Actions\Categories\DeactivateCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;

class DeleteCategory extends Component
{
    public ?int $categoryId = null;
    public string $categoryName = '';

    public function mount(int $categoryId, string $categoryName): void
    {
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
    }

    public function render()
    {
        return view('categories.delete-category');
    }

    public function confirmAction(DeactivateCategory $deactivateCategory): void
    {
        if (! $this->categoryId) {
            $this->dispatch('show-error', 'No se selecciono ninguna categoria.');

            $this->dispatch('close-deactivate-modal');
            return;
        }

        try {
            $deactivateCategory($this->categoryId);

            $this->dispatch('show-message', 'Categoria dada de baja correctamente.');
        } catch (\DomainException $exception) {
            $this->dispatch('show-error', $exception->getMessage());
        } catch (ModelNotFoundException) {
            $this->dispatch('show-error', 'La categoria seleccionada no existe.');
        }

        $this->resetDeactivateState();
    }

    private function resetDeactivateState(): void
    {
        $this->categoryId = null;
        $this->categoryName = '';

        $this->dispatch('close-deactivate-modal');
    }

    public function cancelAction(): void
    {
        $this->dispatch('close-deactivate-modal');
    }
}