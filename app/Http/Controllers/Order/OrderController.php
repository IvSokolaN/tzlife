<?php

namespace App\Http\Controllers\Order;

use App\Enum\OrderStatus;
use App\Exceptions\Product\AvailabilityException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ApproveRequest;
use App\Http\Requests\Order\CreateRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use App\Services\Order\OrderService;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
        //  TODO получить ID аутентифицированного пользователя
        //  $user = auth()->user()->id;

        $userId = $this->getUser()->id;

        $productsRequest = $request->array('products');

        $orderService->store($userId);
        $order = $orderService->getOrder();
        $productService->processProducts($productsRequest, $order, $orderService);
        $order->save();

        return OrderResource::make($order);
    }

    /**
     * @param ApproveRequest $request
     * @return JsonResponse
     */
    public function approve(ApproveRequest $request): JsonResponse
    {
        $user = $this->getUser($request->integer('user_id'));

        $order = Order::query()
            ->where('id', $request->integer('order_id'))
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'error' => 'Заказ не найден',
            ], 400);
        }

        $cost = $order->total_price;
        $userBalance = $user->balance;

        if ($cost > $userBalance) {
            return response()->json([
                'error' => 'Недостаточно средств',
            ], 400);
        }

        DB::transaction(function () use ($order, $cost, $user, $userBalance) {
            $user->update([
                'balance' => $userBalance - $cost,
            ]);
            $order->update([
                'status' => OrderStatus::PAID,
            ]);
            OrderProduct::query()
                ->where('order_id', $order->id)
                ->delete();
        });

        return response()->json([
            'message' => 'Заказ успешно обработан',
        ]);
    }

    /**
     * Нужен только в рамках тестового задания
     *
     * @param int|null $userId
     * @return User
     */
    private function getUser(?int $userId = null): User
    {
        $userQuery = User::query();

        if ($userId) {
            return $userQuery->findOrFail($userId);
        }

        return $userQuery->inRandomOrder()->first();
    }
}
