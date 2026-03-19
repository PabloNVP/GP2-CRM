<?php

namespace Tests\Feature\Products;

use App\Livewire\Products\AddProduct;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_preloads_selected_product_data_in_edit_form(): void
    {
        $category = Category::query()->create([
            'name' => 'CRM',
            'description' => 'Categoria CRM',
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'CRM Core',
            'description' => 'Descripcion inicial',
        ]);

        Livewire::test(AddProduct::class, ['product' => $product])
            ->assertSet('productId', $product->id)
            ->assertSet('categoryId', (string) $category->id)
            ->assertSet('name', 'CRM Core')
            ->assertSet('description', 'Descripcion inicial');
    }

    public function test_it_updates_a_product_successfully(): void
    {
        $category = Category::query()->create([
            'name' => 'ERP',
            'description' => 'Categoria ERP',
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'ERP Base',
            'description' => 'Version inicial',
        ]);

        Livewire::test(AddProduct::class, ['product' => $product])
            ->set('name', 'ERP Base')
            ->set('description', 'Version actualizada')
            ->set('categoryId', (string) $category->id)
            ->call('saveProduct')
            ->assertHasNoErrors()
            ->assertRedirect(route('products.index', absolute: false));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'category_id' => $category->id,
            'name' => 'ERP Base',
            'description' => 'Version actualizada',
        ]);

        $this->assertTrue(session()->has('message'));
    }

    public function test_it_rejects_update_when_name_is_duplicated_in_the_same_category(): void
    {
        $category = Category::query()->create([
            'name' => 'Marketing',
            'description' => 'Categoria Marketing',
        ]);

        Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Automation Pro',
            'description' => 'Producto existente',
        ]);

        $productToUpdate = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Campaign Core',
            'description' => 'Producto a editar',
        ]);

        Livewire::test(AddProduct::class, ['product' => $productToUpdate])
            ->set('categoryId', (string) $category->id)
            ->set('name', 'Automation Pro')
            ->set('description', 'Intento de duplicado')
            ->call('saveProduct')
            ->assertHasErrors(['name' => 'unique'])
            ->assertSee('Ya existe un producto con ese nombre en la categoría seleccionada.')
            ->assertNoRedirect();

        $this->assertDatabaseHas('products', [
            'id' => $productToUpdate->id,
            'name' => 'Campaign Core',
        ]);
    }
}
