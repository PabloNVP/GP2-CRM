<?php

namespace App\Livewire\Products;

use App\Livewire\Actions\Products\InsertProduct;
use App\Livewire\Actions\Products\UpdateProduct;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AddProduct extends Component
{
    public ?Product $product = null;

    public ?int $productId = null;
    public string $name = '';
    public string $description = '';
    public string $categoryId = '';

    public function mount(?Product $product = null): void
    {
        if (! $product || ! $product->exists) {
            return;
        }

        $this->product = $product;
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->description = $product->description ?? '';
        $this->categoryId = (string) $product->category_id;
    }

    public function render()
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('products.add-product', compact('categories'));
    }

    public function saveProduct(InsertProduct $insertProduct, UpdateProduct $updateProduct)
    {
        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $validated = $this->validate();

        $payload = [
            'name' => $validated['name'],
            'description' => $validated['description'] === '' ? null : $validated['description'],
            'category_id' => (int) $validated['categoryId'],
        ];

        $isEditing = $this->isEditing();
        $status = false;

        try {
            if ($isEditing && $this->productId !== null) {
                $status = $updateProduct($payload, $this->productId);
            } else {
                $status = $insertProduct($payload);
            }
        } catch (ModelNotFoundException) {
            session()->flash('error', 'El producto seleccionado no existe.');

            return redirect()->route('products.index');
        }

        if ($status) {
            session()->flash(
                'message',
                $isEditing ? 'Producto actualizado exitosamente.' : 'Producto agregado exitosamente.'
            );
        } else {
            session()->flash(
                'error',
                $isEditing ? 'Hubo un error al actualizar el producto.' : 'Hubo un error al agregar el producto.'
            );
        }

        return redirect()->route('products.index');
    }

    public function rules(): array
    {
        return [
            'categoryId' => ['required', 'integer', 'exists:categories,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')
                    ->ignore($this->productId)
                    ->where(fn ($query) => $query
                        ->where('category_id', (int) $this->categoryId)
                        ->whereNull('deleted_at')
                    ),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function isEditing(): bool
    {
        return $this->productId !== null;
    }

    public function messages(): array
    {
        return [
            'categoryId.required' => 'La categoría es obligatoria.',
            'categoryId.integer' => 'La categoría seleccionada no es válida.',
            'categoryId.exists' => 'La categoría seleccionada no existe.',

            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'name.unique' => 'Ya existe un producto con ese nombre en la categoría seleccionada.',

            'description.string' => 'La descripción debe ser una cadena de texto.',
        ];
    }
}
