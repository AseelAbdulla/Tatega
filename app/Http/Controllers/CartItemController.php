<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\CartDetail;
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
            $request->user(),
            $request->validated()
        );

        return new CartResource($cart);
    }


    /**
     * Update cart item quantity.
     * تم استخدام $id و findOrFail لضمان مطابقة الـ Route برمجياً بدون أخطاء
     */
    public function update(
        UpdateCartItemRequest $request,
        CartDetail $item
    ): CartResource {
        // جلب عنصر السلة بالـ ID الخاص به أو إرجاع 404 إذا لم يكن موجوداً
        // $cartDetail = CartDetail::findOrFail($id);

        $cart = $this->cartService->updateQuantity(
            auth()->user(),
            $item,
            $request->validated()['quantity']
        );

        return new CartResource($cart);
    }


    /**
     * Remove item from cart.
     */
    public function destroy(
        CartDetail $item
    ): JsonResponse {
        $this->cartService->removeItem(
            auth()->user(),
            $item
        );

        return response()->json([
            'status' => true,
            'message' => 'تم حذف المنتج من السلة بنجاح.',
        ]);
    }
}
