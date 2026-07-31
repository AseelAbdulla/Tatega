<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\CartDetail;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;


class CartItemController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Add item to cart.
     */
    public function store(StoreCartItemRequest $request): CartResource
    {
        $cart = $this->cartService->addItem(
            auth()->user(),
            $request->validated()
        );

        return new CartResource($cart);
    }

    /**
     * Update cart item quantity.
     */
    public function update(
        UpdateCartItemRequest $request,
        CartDetail $cartDetail
    ): CartResource {
        $cart = $this->cartService->updateQuantity(
            auth()->user(),
            $cartDetail,
            $request->validated()['quantity']
        );

        return new CartResource($cart);
    }

    /**
     * Remove item from cart.
     */
    public function destroy(CartDetail $cartDetail): JsonResponse
    {
        $this->cartService->removeItem(
            auth()->user(),
            $cartDetail
        );

        return response()->json([
            'status' => true,
            'message' => 'تم حذف المنتج من السلة بنجاح.',
        ]);
    }
}
