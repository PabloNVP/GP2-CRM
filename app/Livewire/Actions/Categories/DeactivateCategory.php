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

        $hasActiveProducts = $category->products()
            ->where('status', StateProductEnum::AVAILABLE->value)
            ->exists();

        if ($hasActiveProducts) {
            throw new \DomainException('No se puede dar de baja la categoria porque tiene productos activos asociados.');
        }

        return $category->delete();
    }
}
