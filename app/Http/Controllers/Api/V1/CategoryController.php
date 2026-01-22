<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;

final class CategoryController extends ApiController
{
    /**
     * Display a listing of top-level categories.
     */
    public function index(): JsonResponse
    {
        $categories = QueryBuilder::for(Category::class)
            ->whereNull('parent_id')
            ->allowedIncludes(['children', 'products'])
            ->orderBy('position')
            ->get();

        return $this->successResponse(
            'Categories retrieved successfully.',
            CategoryResource::collection($categories)
        );
    }

    /**
     * Display the specified category by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $category = QueryBuilder::for(Category::class)
            ->where('slug', $slug)
            ->allowedIncludes(['children', 'products', 'products.variants'])
            ->withCount('products')
            ->first();

        if (! $category) {
            return $this->errorResponse('Category not found.', 404);
        }

        return $this->successResponse(
            'Category details retrieved successfully.',
            new CategoryResource($category)
        );
    }
}
