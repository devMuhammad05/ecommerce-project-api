<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class CollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collections = [
            [
                'name' => 'LOVE Collection',
                'description' => 'A symbol of free-spirited love. Its binding closure and visible screws give it true permanence, while diverse interpretations allow for a unique expression of feelings. Lock in your love, forever.',
                'is_featured' => true,
                'position' => 1,
                'children' => [
                    [
                        'name' => 'LOVE Bracelets',
                        'description' => 'The LOVE bracelet is a flat bangle studded with screws that locks to the wrist. A jewelry icon, the LOVE bracelet can only be opened with a screwdriver.',
                        'is_featured' => false,
                        'position' => 1,
                    ],
                ],
            ],
            [
                'name' => 'Panthère de Cartier',
                'description' => 'The panther, the symbolic animal of Cartier, made its first appearance in the Maison\'s collections in 1914. Jeanne Toussaint, the visionary creative director, was the first to flesh out the creature into three dimensions.',
                'is_featured' => true,
                'position' => 2,
            ],
            [
                'name' => 'Trinity',
                'description' => 'Three rings, three symbols: pink gold for love, yellow gold for fidelity and white gold for friendship. Trinity is a timeless collection that has become an icon of jewelry design.',
                'is_featured' => true,
                'position' => 3,
            ],
        ];

        foreach ($collections as $data) {
            $children = $data['children'] ?? [];
            unset($data['children']);

            $collection = Collection::create([
                ...$data,
                'slug' => Str::slug($data['name']),
            ]);

            foreach ($children as $childData) {
                Collection::create([
                    ...$childData,
                    'parent_id' => $collection->id,
                    'slug' => Str::slug($childData['name']),
                ]);
            }
        }
    }
}
