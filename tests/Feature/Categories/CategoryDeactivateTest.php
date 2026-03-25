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

    public function test_it_does_not_deactivate_category_with_out_of_stock_products(): void
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
            ->call('confirmAction')
            ->assertDispatched('show-error', 'No se puede dar de baja la categoria porque tiene productos asociados.')
            ->assertDispatched('close-deactivate-modal');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Servicios',
        ]);
    }

    public function test_it_does_not_deactivate_category_with_available_products(): void
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
            ->call('confirmAction')
            ->assertDispatched('show-error', 'No se puede dar de baja la categoria porque tiene productos asociados.')
            ->assertDispatched('close-deactivate-modal');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Suscripciones',
        ]);
    }
}
