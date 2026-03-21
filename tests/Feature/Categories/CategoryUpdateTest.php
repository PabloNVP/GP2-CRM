<?php

namespace Tests\Feature\Categories;

use App\Livewire\Categories\AddCategory;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_category_successfully(): void
    {
        $category = Category::query()->create([
            'name' => 'CRM',
            'description' => 'Descripcion inicial',
        ]);

        Livewire::test(AddCategory::class, ['category' => $category])
            ->set('name', 'CRM Enterprise')
            ->set('description', 'Descripcion actualizada')
            ->call('saveCategory')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'CRM Enterprise',
            'description' => 'Descripcion actualizada',
        ]);
    }

    public function test_it_validates_unique_name_on_update(): void
    {
        $current = Category::query()->create([
            'name' => 'CRM',
            'description' => null,
        ]);

        Category::query()->create([
            'name' => 'ERP',
            'description' => null,
        ]);

        Livewire::test(AddCategory::class, ['category' => $current])
            ->set('name', 'ERP')
            ->call('saveCategory')
            ->assertHasErrors(['name' => 'unique']);
    }
}
