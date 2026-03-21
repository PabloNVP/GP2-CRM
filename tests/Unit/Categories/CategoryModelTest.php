<?php

namespace Tests\Unit\Categories;

use App\Enums\StateProductEnum;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_many_products(): void
    {
        $category = Category::query()->create([
            'name' => 'Infraestructura',
            'description' => 'Categoria de infraestructura',
        ]);

        Product::query()->create([
            'name' => 'Producto A',
            'description' => null,
            'stock' => 3,
            'status' => StateProductEnum::AVAILABLE,
            'category_id' => $category->id,
        ]);

        Product::query()->create([
            'name' => 'Producto B',
            'description' => null,
            'stock' => 2,
            'status' => StateProductEnum::OUT_OF_STOCK,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(HasMany::class, $category->products());
        $this->assertCount(2, $category->products);
    }

    public function test_fillable_does_not_include_timestamp_or_soft_delete_columns(): void
    {
        $fillable = (new Category())->getFillable();

        $this->assertNotContains('created_at', $fillable);
        $this->assertNotContains('updated_at', $fillable);
        $this->assertNotContains('deleted_at', $fillable);
    }
}
