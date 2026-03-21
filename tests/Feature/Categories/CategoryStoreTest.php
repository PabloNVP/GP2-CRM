<?php

namespace Tests\Feature\Categories;

use App\Livewire\Categories\AddCategory;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_category_successfully(): void
    {
        Livewire::test(AddCategory::class)
            ->set('name', 'CRM')
            ->set('description', 'Categoria CRM')
            ->call('saveCategory')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'CRM',
            'description' => 'Categoria CRM',
        ]);
    }

    public function test_it_validates_required_and_unique_name_on_create(): void
    {
        Category::query()->create([
            'name' => 'ERP',
            'description' => 'Categoria existente',
        ]);

        Livewire::test(AddCategory::class)
            ->set('name', '')
            ->call('saveCategory')
            ->assertHasErrors(['name' => 'required']);

        Livewire::test(AddCategory::class)
            ->set('name', 'ERP')
            ->call('saveCategory')
            ->assertHasErrors(['name' => 'unique']);
    }
}
