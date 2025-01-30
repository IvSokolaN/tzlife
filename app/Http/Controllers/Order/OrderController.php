<?php

namespace App\Http\Controllers\Order;

use App\Exceptions\Order\OrderNotFoundException;
use App\Exceptions\Product\AvailabilityException;
use App\Exceptions\User\InsufficientFundsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ApproveRequest;
use App\Http\Requests\Order\CreateRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\OrderService;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    )
    {
    }

    /**
     * @param CreateRequest $request
     * @param ProductService $productService
     * @return JsonResponse|OrderResource
     * @throws AvailabilityException
     */
    public function create(
        CreateRequest  $request,
        ProductService $productService): JsonResponse|OrderResource
    {
        //  TODO получить ID аутентифицированного пользователя
        //  $user = auth()->user()->id;

        $userId = $this->getUser()->id;
        $productsRequest = $request->array('products');

        $this->orderService->store($userId);
        $order = $this->orderService->getOrder();
        $productService->processProducts($productsRequest, $order, $this->orderService);
        $order->save();

        return OrderResource::make($order);
    }

    /**
     * @param ApproveRequest $request
     * @return JsonResponse
     * @throws InsufficientFundsException
     * @throws OrderNotFoundException
     */
    public function approve(ApproveRequest $request): JsonResponse
    {
        $user = $this->getUser($request->integer('user_id'));
        $order = $this->orderService->findOrder($request->integer('order_id'), $user->id);
        $cost = $order->total_price;
        $this->orderService->checkUserBalance($user->balance, $cost);
        $this->orderService->approveOrder($order, $user, $cost);

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
