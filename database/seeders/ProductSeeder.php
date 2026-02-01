<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Collection as ModelCollection;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // 1. Get reference data
        $categories = Category::all();
        $collections = ModelCollection::all();
        $attributeValues = AttributeValue::all();

        // 2. Define High-Quality Luxury Products
        $luxuryProducts = [
            [
                'name' => 'LOVE Ring',
                'category' => 'Rings',
                'collection' => 'LOVE Collection',
                'attributes' => ['Platinum', 'Men'],
                'variants' => [
                    ['sku' => 'CR-LOVE-PL-01', 'price' => 250000, 'quantity' => 5],
                    ['sku' => 'CR-LOVE-PL-02', 'price' => 250000, 'quantity' => 3],
                ],
            ],
            [
                'name' => 'Trinity Necklace',
                'category' => 'Necklaces',
                'collection' => 'Trinity',
                'attributes' => ['Rose Gold', 'Women'],
                'variants' => [
                    ['sku' => 'CR-TRIN-RG-01', 'price' => 180000, 'quantity' => 10],
                ],
            ],
            [
                'name' => 'Panthère de Cartier Bracelet',
                'category' => 'Bracelets',
                'collection' => 'Panthère de Cartier',
                'attributes' => ['White Gold', 'Women', 'Emerald'],
                'variants' => [
                    ['sku' => 'CR-PAN-WG-01', 'price' => 850000, 'quantity' => 2],
                ],
            ],
            [
                'name' => 'Iconic Watch',
                'category' => 'Men\'s Watches',
                'collection' => 'Panthère de Cartier',
                'attributes' => ['Platinum', 'Men'],
                'variants' => [
                    ['sku' => 'CR-WAT-PL-01', 'price' => 1500000, 'quantity' => 1],
                ],
            ],
            [
                'name' => 'Cushion Cut Diamond Ring',
                'category' => 'Rings',
                'collection' => 'Trinity',
                'attributes' => ['White Gold', 'Women', 'Cushion'],
                'variants' => [
                    ['sku' => 'CR-DIA-CUSH-01', 'price' => 1200000, 'quantity' => 4],
                ],
            ],
        ];

        foreach ($luxuryProducts as $pData) {
            $product = Product::create([
                'name' => $pData['name'],
                'slug' => Str::slug($pData['name']),
                'description' => "An exquisite {$pData['name']} representing the pinnacle of luxury craftsmanship.",
                'status' => ProductStatus::Active,
                'is_featured' => true,
            ]);

            // Attach Category
            $category = $categories->firstWhere('name', $pData['category']);
            if ($category) {
                $product->categories()->attach($category->id);
                // Also attach parent category if it exists
                if ($category->parent_id) {
                    $product->categories()->attach($category->parent_id);
                }
            }

            // Attach Collection
            $collection = $collections->firstWhere('name', $pData['collection']);
            if ($collection) {
                $product->collections()->attach($collection->id, ['position' => rand(1, 10)]);
            }

            // Attach Attributes
            foreach ($pData['attributes'] as $attrValueName) {
                $attrVal = $attributeValues->firstWhere('value', $attrValueName);
                if ($attrVal) {
                    $product->attributeValues()->attach($attrVal->id);
                }
            }

            // Create Variants
            foreach ($pData['variants'] as $vData) {
                $product->variants()->create([
                    'sku' => $vData['sku'],
                    'price' => $vData['price'],
                    'quantity' => $vData['quantity'],
                    // Standard luxury dimensions
                    // 'weight' => 0.5,
                    // 'height' => 1.0,
                    // 'width' => 1.0,
                    // 'depth' => 1.0,
                ]);
            }
        }

        // 3. Generate some random products to fill out the catalog
        Product::factory(10)->create()->each(function (Product $product) use ($categories, $collections, $attributeValues) {
            $product->categories()->attach($categories->random()->id);
            $product->collections()->attach($collections->random()->id);

            // Attach 2-3 random attributes
            $product->attributeValues()->attach(
                $attributeValues->random(rand(2, 3))->pluck('id')->toArray()
            );

            // Create 1-2 variants
            Variant::factory(rand(1, 2))->create(['product_id' => $product->id]);
        });
    }
}
