<?php

namespace App\Livewire\Actions\Categories;

use App\Livewire\Actions\BaseUpdateAction;
use App\Models\Category;

final readonly class UpdateCategory extends BaseUpdateAction
{
    /**
     * @return class-string<Category>
     */
    protected function modelClass(): string
    {
        return Category::class;
    }
}