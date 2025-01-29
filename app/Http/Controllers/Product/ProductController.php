<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * @param PaginateRequest $paginateRequest
     * @return AnonymousResourceCollection
     */
    public function catalog(PaginateRequest $paginateRequest): AnonymousResourceCollection
    {
        $currentPage = $paginateRequest->integer('page', 1);
        $itemsPerPage = $paginateRequest->integer('per_page', 10);

        $products = Product::query()
            ->with('warehouses')
            ->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        return ProductResource::collection($products);
    }
}
