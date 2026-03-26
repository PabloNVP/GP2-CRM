<?php

namespace Tests\Browser;

use App\Enums\StateProductEnum;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProductsTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_it_completes_critical_flow_category_product_listing_edit_and_logical_deactivate(): void
    {
        $user = User::factory()->create();

        $categoryName = 'Categoria E2E '.uniqid();
        $productName = 'Producto E2E '.uniqid();
        $updatedDescription = 'Descripcion actualizada por Dusk';

        $this->browse(function (Browser $browser) use ($user, $categoryName, $productName, $updatedDescription) {
            $browser->loginAs($user)
                ->visit('/categories')
                ->assertSee('Categorias')
                ->clickLink('Agregar categoria')
                ->waitForText('Agregar Categoria')
                     ->type('#name', $categoryName)
                     ->type('#description', 'Categoria creada en flujo E2E')
                ->click('button[type="submit"]')
                ->waitForLocation('/categories')
                ->waitForText('Categoria agregada exitosamente.')
                ->assertSee($categoryName);

            $categoryId = (int) Category::query()
                ->where('name', $categoryName)
                ->value('id');

            $browser->visit('/products')
                ->assertSee('Productos')
                ->clickLink('Agregar producto')
                ->waitForText('Agregar Producto')
                     ->select('#categoryId', (string) $categoryId)
                     ->type('#name', $productName)
                     ->type('#description', 'Descripcion inicial de producto E2E')
                ->click('button[type="submit"]')
                ->waitForLocation('/products')
                ->waitForText('Producto agregado exitosamente.')
                ->assertSee($productName)
                ->clickLink('Editar')
                ->waitForText('Editar Producto')
                     ->type('#description', $updatedDescription)
                ->click('button[type="submit"]')
                ->waitForLocation('/products')
                ->waitForText('Producto actualizado exitosamente.')
                ->press('Desactivar')
                ->waitForText('Confirmar desactivacion')
                ->press('Confirmar')
                ->waitForText('Producto actualizado de estado correctamente.');
        });

        $product = Product::query()->where('name', $productName)->first();

        $this->assertNotNull($product);
        $this->assertSame($updatedDescription, $product->description);
        $this->assertSame(StateProductEnum::OUT_OF_STOCK, $product->status);
    }
}
