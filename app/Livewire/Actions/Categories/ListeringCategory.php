<?php

namespace App\Livewire\Actions\Categories;

use App\Models\Category;

final readonly class ListeringCategory
{
    public function __invoke(): iterable
    {
        return Category::query()
            ->withCount('products')
            ->orderByDesc('id')
            ->paginate(10);
    }
}
