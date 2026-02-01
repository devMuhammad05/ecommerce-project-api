<?php

declare(strict_types=1);

namespace Tests\Unit\Traits;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('HasSlug Trait', function () {
    test('it generates a slug from the name', function () {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test description',
            'status' => 'draft',
        ]);

        expect($product->slug)->toBe('test-product');
    });

    test('it generates unique slugs when duplicates exist', function () {
        $product1 = Product::create([
            'name' => 'Duplicate Name',
            'description' => 'Test',
            'status' => 'draft',
        ]);

        $product2 = Product::create([
            'name' => 'Duplicate Name',
            'description' => 'Test',
            'status' => 'draft',
        ]);

        $product3 = Product::create([
            'name' => 'Duplicate Name',
            'description' => 'Test',
            'status' => 'draft',
        ]);

        expect($product1->slug)->toBe('duplicate-name');
        expect($product2->slug)->toBe('duplicate-name-1');
        expect($product3->slug)->toBe('duplicate-name-2');
    });

    test('it updates slug when source column changes', function () {
        $product = Product::create([
            'name' => 'Original Name',
            'description' => 'Test',
            'status' => 'draft',
        ]);

        expect($product->slug)->toBe('original-name');

        $product->update(['name' => 'Updated Name']);

        expect($product->fresh()->slug)->toBe('updated-name');
    });

    test('it preserves manual slug when source changes', function () {
        $product = Product::create([
            'name' => 'Original Name',
            'slug' => 'custom-slug',
            'description' => 'Test',
            'status' => 'draft',
        ]);

        expect($product->slug)->toBe('custom-slug');

        $product->update(['name' => 'Updated Name']);

        expect($product->fresh()->slug)->toBe('custom-slug');
    });

    test('it generates unique slug when updating to duplicate name', function () {
        $product1 = Product::create([
            'name' => 'First Product',
            'description' => 'Test',
            'status' => 'draft',
        ]);

        $product2 = Product::create([
            'name' => 'Second Product',
            'description' => 'Test',
            'status' => 'draft',
        ]);

        expect($product1->slug)->toBe('first-product');
        expect($product2->slug)->toBe('second-product');

        $product2->update(['name' => 'First Product']);

        expect($product2->fresh()->slug)->toBe('first-product-1');
    });
});
