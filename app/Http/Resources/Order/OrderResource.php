<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Warehouse\WarehouseResource;
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
            'created_at' => $this->created_at,
            'number' => $this->number,
            'status' => $this->status,
            'total_price' => $this->total_price,
        ];
    }
}
