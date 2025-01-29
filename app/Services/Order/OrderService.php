<?php

namespace App\Services\Order;

use App\Enum\OrderStatus;
use App\Models\Order;
use App\Models\Product;

class OrderService
{
    private Order $order;

    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param int $userId
     * @return void
     */
    public function store(int $userId): void
    {
        $this->order = Order::query()->create([
            'number' => fake()->unique()->ean8(),
            'status' => OrderStatus::PROCESSING,
            'total_price' => 0,
            'user_id' => $userId,
        ]);
    }

    public function reserveProduct(Product $product, int $quantity): void
    {
        $this->order->products()->attach($product->id, [
            'quantity' => $quantity,
            'price' => $product->price,
        ]);
    }
}
