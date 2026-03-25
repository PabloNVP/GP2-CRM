<?php

namespace App\Livewire\Actions\Products;

use App\Livewire\Actions\BaseInsertAction;
use App\Models\Product;

final readonly class InsertProduct extends BaseInsertAction
{
    /**
     * @return class-string<Product>
     */
    protected function modelClass(): string
    {
        return Product::class;
    }
}