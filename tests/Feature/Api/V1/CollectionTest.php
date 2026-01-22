<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\Collection;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Collection API', function () {
    test('it can list top-level collections', function () {
        Collection::factory()->count(3)->create(['parent_id' => null]);

        $response = $this->getJson('/api/v1/collections');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'hero_image',
                        'position',
                    ],
                ],
            ]);
    });

    test('it can show a collection with its children', function () {
        $parent = Collection::factory()->create();
        $child = Collection::factory()->create(['parent_id' => $parent->id]);

        $response = $this->getJson("/api/v1/collections/{$parent->slug}?include=children");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', $parent->name)
            ->assertJsonCount(1, 'data.children');
    });

    test('it can show a collection with its products', function () {
        $collection = Collection::factory()->create();
        $product = Product::factory()->create();
        $collection->products()->attach($product);

        $response = $this->getJson("/api/v1/collections/{$collection->slug}?include=products");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', $collection->name)
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.name', $product->name);
    });

    test('it returns 404 for non-existent collection', function () {
        $response = $this->getJson('/api/v1/collections/non-existent-collection');

        $response->assertStatus(404);
    });
});
