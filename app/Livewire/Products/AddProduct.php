<?php

namespace App\Livewire\Products;

use App\Livewire\Actions\Products\UpsertProduct;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AddProduct extends Component
{
    public string $name = '';
    public string $description = '';
    public string $categoryId = '';

    public function render()
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('products.add-product', compact('categories'));
    }

    public function saveProduct(UpsertProduct $upsertProduct)
    {
        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $validated = $this->validate();

        $payload = [
            'name' => $validated['name'],
            'description' => $validated['description'] === '' ? null : $validated['description'],
            'category_id' => (int) $validated['categoryId'],
        ];

        $status = $upsertProduct($payload);

        if ($status) {
            session()->flash('message', 'Producto agregado exitosamente.');
        } else {
            session()->flash('error', 'Hubo un error al agregar el producto.');
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
                    ->where(fn ($query) => $query
                        ->where('category_id', (int) $this->categoryId)
                        ->whereNull('deleted_at')
                    ),
            ],
            'description' => ['nullable', 'string'],
        ];
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
