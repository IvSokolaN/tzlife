<?php

namespace App\Services\Product;

use App\Exceptions\Product\AvailabilityException;
use App\Models\Order;
use App\Models\Product;
use App\Services\Order\OrderService;

class ProductService
{
    /**
     * @param Product $product
     * @param int $quantity
     * @return void
     */
    public function updateQuantity(Product $product, int $quantity): void
    {
        $warehouses = $product->warehouses;
        foreach ($warehouses as $warehouse) {
            if ($quantity <= 0) {
                break;
            }

            $availableQuantity = $warehouse->pivot->quantity;
            if ($availableQuantity > 0) {
                if ($availableQuantity >= $quantity) {
                    $warehouse->pivot->quantity -= $quantity;
                    $warehouse->pivot->save();
                    $quantity = 0;
                } else {
                    $quantity -= $availableQuantity;
                    $warehouse->pivot->quantity = 0;
                    $warehouse->pivot->save();
                }
            }
        }
    }

    /**
     * @param array $productsRequest
     * @param Order $order
     * @param OrderService $orderService
     * @return void
     * @throws AvailabilityException
     */
    public function processProducts(array $productsRequest, Order $order, OrderService $orderService): void
    {
        foreach ($productsRequest as $productRequest) {
            $product = Product::query()
                ->with('warehouses')
                ->findOrFail($productRequest['id']);

            $quantityRequest = $productRequest['quantity'];

            $this->checkAvailability($product, $quantityRequest, $order);
            $orderService->reserveProduct($product, $quantityRequest);
            $this->updateQuantity($product, $quantityRequest);

            $order->total_price += $product->price * $quantityRequest;
        }
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @param Order $order
     * @throws AvailabilityException
     */
    private function checkAvailability(Product $product, int $quantity, Order $order): void
    {
        $totalQuantityProduct = $product->warehouses->sum('pivot.quantity');
        if ($quantity > $totalQuantityProduct) {
            $order->delete();

            throw new AvailabilityException([
                'productName' => $product->name,
                'totalQuantityProduct' => $totalQuantityProduct,
            ]);
        }
    }
}
