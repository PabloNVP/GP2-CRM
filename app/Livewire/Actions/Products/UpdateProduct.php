<?php

namespace App\Livewire\Actions\Products;

use App\Livewire\Actions\BaseUpdateAction;
use App\Models\Product;

final readonly class UpdateProduct extends BaseUpdateAction
{
    /**
     * @return class-string<Product>
     */
    protected function modelClass(): string
    {
        return Product::class;
    }
}