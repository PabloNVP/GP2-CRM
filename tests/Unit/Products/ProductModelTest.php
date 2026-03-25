<?php

namespace Tests\Unit\Products;

use App\Enums\StateProductEnum;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_is_cast_to_enum(): void
    {
        $product = Product::query()->create([
            'name' => 'Producto Cast',
            'description' => 'Descripcion',
            'stock' => 10,
            'status' => StateProductEnum::AVAILABLE,
            'category_id' => null,
        ]);

        $this->assertInstanceOf(StateProductEnum::class, $product->status);
        $this->assertSame(StateProductEnum::AVAILABLE, $product->status);
    }

    public function test_it_belongs_to_category(): void
    {
        $category = Category::query()->create([
            'name' => 'Software',
            'description' => null,
        ]);

        $product = Product::query()->create([
            'name' => 'Producto Relacion',
            'description' => 'Descripcion',
            'stock' => 5,
            'status' => StateProductEnum::AVAILABLE,
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $product->category());
        $this->assertSame($category->id, $product->category?->id);
    }

    public function test_fillable_does_not_include_timestamp_or_soft_delete_columns(): void
    {
        $fillable = (new Product())->getFillable();

        $this->assertNotContains('created_at', $fillable);
        $this->assertNotContains('updated_at', $fillable);
        $this->assertNotContains('deleted_at', $fillable);
    }
}
