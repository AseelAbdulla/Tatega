<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $cart = $this->cartService->getCurrentCart($user);

        return new CartResource($cart);
    }

    public function count(Request $request)
    {
        $userId = $request->user()?->id;
        $count = $this->cartService->getCartCount($userId);

        return response()->json([
            'status' => true,
            'cart_count' => (int) $count,
        ], 200);
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
