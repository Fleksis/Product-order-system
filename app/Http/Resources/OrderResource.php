<?php

namespace App\Http\Resources;

use App\Http\Resources\Product\OrderProductResource;
use App\Http\Resources\User\UserWithoutRolesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user' => new UserWithoutRolesResource($this->user),
            'status' => $this->status,
            'total' => $this->total,
            'products' => OrderProductResource::collection($this->products),
        ];
    }
}
