<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Addresses\StoreAddressAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Addresses\StoreAddressRequest;
use App\Http\Resources\Api\V1\AddressResource;
use Illuminate\Http\JsonResponse;

final class AddressController extends ApiController
{
    public function store(StoreAddressRequest $request, StoreAddressAction $action): JsonResponse
    {
        $address = $action->execute(
            userId: (int) $request->user()->id,
            data: $request->validated()
        );

        return $this->successResponse(
            message: 'Address stored successfully.',
            data: new AddressResource($address)
        );
    }
}
