<?php

namespace App\Livewire\Categories;

use App\Livewire\Actions\Categories\InsertCategory;
use App\Livewire\Actions\Categories\UpdateCategory;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AddCategory extends Component
{
    public ?Category $category = null;

    public ?int $categoryId = null;
    public string $name = '';
    public string $description = '';

    public function mount(?Category $category = null): void
    {
        if (! $category || ! $category->exists) {
            return;
        }

        $this->category = $category;
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
    }

    public function render()
    {
        return view('categories.add-category');
    }

    public function saveCategory(InsertCategory $insertCategory, UpdateCategory $updateCategory)
    {
        $this->name = trim($this->name);
        $this->description = trim($this->description);

        $validated = $this->validate();

        $payload = [
            'name' => $validated['name'],
            'description' => $validated['description'] === '' ? null : $validated['description'],
        ];

        $isEditing = $this->isEditing();
        $status = false;

        try {
            if ($isEditing && $this->categoryId !== null) {
                $status = $updateCategory($payload, $this->categoryId);
            } else {
                $status = $insertCategory($payload);
            }
        } catch (ModelNotFoundException) {
            session()->flash('error', 'La categoria seleccionada no existe.');

            return redirect()->route('categories.index');
        }

        if ($status) {
            session()->flash(
                'message',
                $isEditing ? 'Categoria actualizada exitosamente.' : 'Categoria agregada exitosamente.'
            );
        } else {
            session()->flash(
                'error',
                $isEditing ? 'Hubo un error al actualizar la categoria.' : 'Hubo un error al agregar la categoria.'
            );
        }

        return redirect()->route('categories.index');
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($this->categoryId),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la categoria es obligatorio.',
            'name.string' => 'El nombre de la categoria debe ser una cadena de texto.',
            'name.max' => 'El nombre de la categoria no puede tener mas de 255 caracteres.',
            'name.unique' => 'Ya existe una categoria con ese nombre.',
            'description.string' => 'La descripcion debe ser una cadena de texto.',
        ];
    }

    public function isEditing(): bool
    {
        return $this->categoryId !== null;
    }
}
