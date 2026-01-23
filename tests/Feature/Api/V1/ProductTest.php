<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Product API', function () {
    test('it can show a product by slug with all required details', function () {
        $product = Product::factory()->create();

        $category = Category::factory()->create();
        $product->categories()->attach($category);

        $collection = Collection::factory()->create();
        $product->collections()->attach($collection);

        Variant::factory()->create(['product_id' => $product->id]);

        $attribute = Attribute::factory()->create(['name' => 'Material']);
        $attributeValue = AttributeValue::factory()->create([
            'attribute_id' => $attribute->id,
            'value' => 'Gold',
            'slug' => 'gold',
        ]);
        $product->attributeValues()->attach($attributeValue);

        $response = $this->getJson("/api/v1/products/{$product->slug}?include=variants,categories,collections,attributeValues");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', $product->name)
            ->assertJsonPath('data.slug', $product->slug)
            ->assertJsonCount(1, 'data.variants')
            ->assertJsonCount(1, 'data.categories')
            ->assertJsonCount(1, 'data.collections')
            ->assertJsonCount(1, 'data.attribute_values');
    });

    test('it returns 404 for non-existent product', function () {
        $response = $this->getJson('/api/v1/products/non-existent-product');

        $response->assertStatus(404);
    });
});
