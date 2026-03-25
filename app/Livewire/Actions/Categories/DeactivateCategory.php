<?php

namespace App\Livewire\Actions\Categories;

use App\Enums\StateProductEnum;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class DeactivateCategory
{
    /**
     * @throws \DomainException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function __invoke(int $categoryId): bool
    {
        $category = Category::query()->find($categoryId);

        if (! $category) {
            throw (new ModelNotFoundException())->setModel(Category::class, [$categoryId]);
        }

        $hasProducts = $category->products()
            ->exists();

        if ($hasProducts) {
            throw new \DomainException('No se puede dar de baja la categoria porque tiene productos asociados.');
        }

        return $category->delete();
    }
}
