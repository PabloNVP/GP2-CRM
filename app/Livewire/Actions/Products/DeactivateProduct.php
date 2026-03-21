<?php

namespace App\Livewire\Actions\Products;

use App\Enums\StateProductEnum;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class DeactivateProduct
{
    /**
     * Aplica baja logica de producto.
     *
     * Disponible -> Sin stock
     * Sin stock -> Descontinuado
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function __invoke(int $productId): bool
    {
        $product = Product::query()->find($productId);

        if (! $product) {
            throw (new ModelNotFoundException())->setModel(Product::class, [$productId]);
        }

        $nextStatus = match ($product->status) {
            StateProductEnum::AVAILABLE => StateProductEnum::OUT_OF_STOCK,
            StateProductEnum::OUT_OF_STOCK => StateProductEnum::DISCONTINUED,
            default => StateProductEnum::DISCONTINUED,
        };

        return $product->update([
            'status' => $nextStatus,
        ]);
    }
}
