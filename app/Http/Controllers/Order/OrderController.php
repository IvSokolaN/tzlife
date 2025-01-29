<?php

namespace App\Http\Controllers\Order;

use App\Exceptions\Product\AvailabilityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\User;
use App\Services\Order\OrderService;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * @param CreateRequest $request
     * @param OrderService $orderService
     * @param ProductService $productService
     * @return JsonResponse|OrderResource
     * @throws AvailabilityException
     */
    public function create(
        CreateRequest  $request,
        OrderService   $orderService,
        ProductService $productService): JsonResponse|OrderResource
    {
        $userId = User::query()
            ->inRandomOrder()
            ->value('id');
        $productsRequest = $request->array('products');

        $orderService->store($userId);
        $order = $orderService->getOrder();
        $productService->processProducts($productsRequest, $order, $orderService);
        $order->save();

        return OrderResource::make($order);
    }
}
