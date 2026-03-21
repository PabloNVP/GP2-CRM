<?php

namespace Tests\Feature\Categories;

use App\Enums\StateProductEnum;
use App\Livewire\Categories\DeleteCategory;
use App\Livewire\Categories\IndexCategory;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryDeactivateTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_category_closes_modal_when_receiving_close_event(): void
    {
        Livewire::test(IndexCategory::class)
            ->call('openDeactivateModal', 1, 'Categoria Demo')
            ->assertSet('isDeactivateModalVisible', true)
            ->dispatch('close-deactivate-modal')
            ->assertSet('isDeactivateModalVisible', false);
    }

    public function test_it_deactivates_category_without_active_products(): void
    {
        $category = Category::query()->create([
            'name' => 'Servicios',
            'description' => null,
        ]);

        Product::query()->create([
            'name' => 'Servicio Legacy',
            'description' => null,
            'status' => StateProductEnum::OUT_OF_STOCK,
            'category_id' => $category->id,
        ]);

        Livewire::test(DeleteCategory::class, [
            'categoryId' => $category->id,
            'categoryName' => $category->name,
        ])
            ->call('confirmDeactivate')
            ->assertDispatched('show-message', 'Categoria dada de baja correctamente.')
            ->assertDispatched('close-deactivate-modal');

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_it_does_not_deactivate_category_with_active_products(): void
    {
        $category = Category::query()->create([
            'name' => 'Suscripciones',
            'description' => null,
        ]);

        Product::query()->create([
            'name' => 'Plan Pro',
            'description' => null,
            'status' => StateProductEnum::AVAILABLE,
            'category_id' => $category->id,
        ]);

        Livewire::test(DeleteCategory::class, [
            'categoryId' => $category->id,
            'categoryName' => $category->name,
        ])
            ->call('confirmDeactivate')
            ->assertDispatched('show-error', 'No se puede dar de baja la categoria porque tiene productos activos asociados.')
            ->assertDispatched('close-deactivate-modal');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Suscripciones',
        ]);
    }
}
