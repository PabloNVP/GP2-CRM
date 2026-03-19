<?php

namespace App\Livewire\Actions\Products;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class UpsertProduct
{
    /**
     * Crea o actualiza un producto específico.
     *
     * @param  array<string, mixed>  $payload
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function __invoke(array $payload, ?int $productId = null): bool
    {
        if ($productId === null) {
            return Product::query()->create($payload) !== null;
        }

        $product = Product::query()->find($productId);

        if (! $product) {
            throw (new ModelNotFoundException())->setModel(Product::class, [$productId]);
        }

        $product->fill($payload);

        // Considera éxito cuando no hay cambios para evitar falsos errores en edición.
        return $product->isDirty() ? $product->save() : true;
    }
}
