<?php

namespace App\Services\Order;

use App\Enum\OrderStatus;
use App\Exceptions\Order\OrderNotFoundException;
use App\Exceptions\User\InsufficientFundsException;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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

    /**
     * @param Product $product
     * @param int $quantity
     * @return void
     */
    public function reserveProduct(Product $product, int $quantity): void
    {
        $this->order->products()->attach($product->id, [
            'quantity' => $quantity,
            'price' => $product->price,
        ]);
    }

    /**
     * @param int $orderId
     * @param int $userId
     * @return Order
     * @throws OrderNotFoundException
     */
    public function findOrder(int $orderId, int $userId): Order
    {
        $order = Order::query()
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            throw new OrderNotFoundException();
        }

        return $order;
    }

    /**
     * @param Order $order
     * @param User $user
     * @param float $cost
     * @return void
     */
    public function approveOrder(Order $order, User $user, float $cost): void
    {
        DB::transaction(function () use ($order, $cost, $user) {
            $user->update([
                'balance' => $user->balance - $cost,
            ]);
            $order->update([
                'status' => OrderStatus::PAID,
            ]);
            OrderProduct::query()
                ->where('order_id', $order->id)
                ->delete();
        });
    }

    /**
     * @param $userBalance
     * @param $cost
     * @return void
     * @throws InsufficientFundsException
     */
    public function checkUserBalance($userBalance, $cost): void
    {
        if ($cost > $userBalance) {
            throw new InsufficientFundsException();
        }
    }
}
