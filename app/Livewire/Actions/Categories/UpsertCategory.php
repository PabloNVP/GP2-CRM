<?php

namespace App\Livewire\Actions\Categories;

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class UpsertCategory
{
    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function __invoke(array $payload, ?int $categoryId = null): bool
    {
        if ($categoryId === null) {
            return Category::query()->create($payload) !== null;
        }

        $category = Category::query()->find($categoryId);

        if (! $category) {
            throw (new ModelNotFoundException())->setModel(Category::class, [$categoryId]);
        }

        $category->fill($payload);

        return $category->isDirty() ? $category->save() : true;
    }
}
