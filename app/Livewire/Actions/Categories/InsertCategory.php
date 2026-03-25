<?php

namespace App\Livewire\Actions\Categories;

use App\Livewire\Actions\BaseInsertAction;
use App\Models\Category;

final readonly class InsertCategory extends BaseInsertAction
{
    /**
     * @return class-string<Category>
     */
    protected function modelClass(): string
    {
        return Category::class;
    }
}