<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\V1\CollectionResource;
use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\QueryBuilder;

final class CollectionController extends ApiController
{
    /**
     * Display a listing of top-level collections.
     */
    public function index(): JsonResponse
    {
        $collections = QueryBuilder::for(Collection::class)
            ->whereNull('parent_id')
            ->allowedIncludes(['children', 'products'])
            ->orderBy('position')
            ->get();

        return $this->successResponse(
            'Collections retrieved successfully.',
            CollectionResource::collection($collections)
        );
    }

    /**
     * Display the specified collection by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $collection = QueryBuilder::for(Collection::class)
            ->where('slug', $slug)
            ->allowedIncludes(['children', 'products', 'products.variants'])
            ->first();

        if (! $collection) {
            return $this->errorResponse('Collection not found.', 404);
        }

        return $this->successResponse(
            'Collection details retrieved successfully.',
            new CollectionResource($collection)
        );
    }
}
