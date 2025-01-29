<?php

namespace App\Exceptions\Product;

use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityException extends Exception
{
    protected array $additionalData;

    public function __construct($additionalData = [])
    {
        parent::__construct();
        $this->additionalData = $additionalData;
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => 'Недостаточно товара: '
                . $this->additionalData['productName']
                . '. Максимум доступно: ' . $this->additionalData['totalQuantityProduct'],
        ], 400);
    }
}
