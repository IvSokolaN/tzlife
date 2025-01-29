<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\Warehouse\WarehouseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'created_at' => $this->created_at,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'total_quantity' => $this->warehouses->sum('pivot.quantity'),
            'warehouses' => WarehouseResource::collection($this->warehouses),
        ];
    }
}
