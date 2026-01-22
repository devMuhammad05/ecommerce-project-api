<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Category */
final class CategoryResource extends JsonResource
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
            'image_url' => $this->image_url,
            'position' => $this->position,
            'parent_id' => $this->parent_id,
            'children' => $this->whenLoaded('children', fn() => CategoryResource::collection($this->children)),
            'products' => $this->whenLoaded('products', fn() => ProductResource::collection($this->products)),
            'products_count' => $this->whenCounted('products'),

            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}
