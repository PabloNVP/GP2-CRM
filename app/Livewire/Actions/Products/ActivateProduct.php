<?php

namespace App\Livewire\Actions\Products;

use App\Enums\StateProductEnum;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class ActivateProduct
{
    /**
     * Activa un producto.
     *
     * Descontinuado -> Disponible
     * Sin stock -> Disponible
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
            StateProductEnum::DISCONTINUED => StateProductEnum::AVAILABLE,
            StateProductEnum::OUT_OF_STOCK => StateProductEnum::AVAILABLE,
            default => StateProductEnum::AVAILABLE,
        };

        return $product->update([
            'status' => $nextStatus,
        ]);
    }
}
