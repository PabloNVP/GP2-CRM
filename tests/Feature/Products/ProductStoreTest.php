<?php

namespace Tests\Feature\Products;

use App\Livewire\Products\AddProduct;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ProductStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_product_with_unit_price(): void
    {
        $category = Category::query()->create([
            'name' => 'Ventas',
            'description' => 'Categoria Comercial',
        ]);

        Livewire::test(AddProduct::class)
            ->set('categoryId', (string) $category->id)
            ->set('name', 'CRM Premium')
            ->set('description', 'Producto con precio definido')
            ->set('unitPrice', '149.99')
            ->call('saveProduct')
            ->assertHasNoErrors()
            ->assertRedirect(route('products.index', absolute: false));

        $this->assertDatabaseHas('products', [
            'category_id' => $category->id,
            'name' => 'CRM Premium',
            'unit_price' => 149.99,
        ]);
    }

    public function test_it_stores_a_product_successfully(): void
    {
        $category = Category::query()->create([
            'name' => 'CRM',
            'description' => 'Categoria CRM',
        ]);

        Livewire::test(AddProduct::class)
            ->set('categoryId', (string) $category->id)
            ->set('name', 'CRM Core')
            ->set('description', 'Producto para gestión comercial')
            ->call('saveProduct')
            ->assertHasNoErrors()
            ->assertRedirect(route('products.index', absolute: false));

        $this->assertDatabaseHas('products', [
            'category_id' => $category->id,
            'name' => 'CRM Core',
            'description' => 'Producto para gestión comercial',
        ]);

        $this->assertTrue(session()->has('message'));
    }

    public function test_it_rejects_store_when_name_is_duplicated_in_the_same_category(): void
    {
        $category = Category::query()->create([
            'name' => 'ERP',
            'description' => 'Categoria ERP',
        ]);

        DB::table('products')->insert([
            'category_id' => $category->id,
            'name' => 'Suite ERP',
            'description' => 'Producto existente',
            'status' => 'Disponible',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        Livewire::test(AddProduct::class)
            ->set('categoryId', (string) $category->id)
            ->set('name', 'Suite ERP')
            ->set('description', 'Producto duplicado')
            ->call('saveProduct')
            ->assertHasErrors(['name' => 'unique'])
            ->assertSee('Ya existe un producto con ese nombre en la categoría seleccionada.')
            ->assertNoRedirect();

        $this->assertDatabaseCount('products', 1);
    }
}
