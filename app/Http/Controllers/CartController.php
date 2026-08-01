<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;


class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Display the authenticated user's cart.
     */
    public function index(): CartResource
    {
        $user = auth()->user();
        $cart = $this->cartService->getCurrentCart($user) ;

        return new CartResource($cart);
    }

    /**
     * Clear all cart items.
     */
    public function clear(): JsonResponse
    {
        $this->cartService->clearCart(auth()->user());

        return response()->json([
            'status' => true,
            'message' => 'تم تفريغ السلة بنجاح.',
        ]);
    }
}

