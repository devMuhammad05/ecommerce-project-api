<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Collection */
final class CollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'hero_image' => $this->hero_image,
            'is_featured' => $this->is_featured,
            'position' => $this->position,
            'parent_id' => $this->parent_id,
            'children' => $this->whenLoaded('children', fn () => CollectionResource::collection($this->children)),
            'products' => $this->whenLoaded('products', fn () => ProductResource::collection($this->products)),
        ];
    }
}
