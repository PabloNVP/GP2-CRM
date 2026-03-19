<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class IndexProducts extends Component
{
    use WithPagination;

    public function render()
    {
        $products = Product::query()
            ->with('category')
            ->orderByDesc('id')
            ->paginate(10);

        return view('products.index-products', compact('products'));
    }
}
