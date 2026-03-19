<?php

namespace App\Livewire\Actions\Products;

use App\Models\Product;

final readonly class ListeringProduct
{
    /**
     * Lista los productos activos.
     *
     * @return \Illuminate\Database\Eloquent\Collection|Product[]
     */
    public function __invoke(
        string $search = '',
        string $statusFilter = '',
        string $categoryFilter = '',
    ): iterable
    {
        $query = Product::query()->with('category')->orderByDesc('id');

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if ($categoryFilter !== '') {
            $query->where('category_id', (int) $categoryFilter);
        }

        $search = trim($search);

        if (mb_strlen($search) >= 3) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate(10);
    }
}