<?php

namespace App\Services;


use App\Models\Cart;
use App\Models\User;
use App\Models\CartDetail;
use App\Models\ProductUnit;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartService
{

    /**
     * الحصول على سلة المستخدم الحالية
     */
    public function getCurrentCart(User $user): Cart
    {
        return Cart::firstOrCreate([
            'user_id' => $user->id 
        ])
        ->load([
            'details.product.images:id,product_id,image_path',
            'details.product.category:id,name',
            'details.product:id,category_id,name,sku',
            'details.unit:id,unit_name'
        ]);
    }



    /**
     * إضافة منتج إلى السلة
     */
    public function addItem(User $user, array $data): Cart
    {

        return DB::transaction(function () use ($user, $data) {


            // إنشاء أو جلب سلة المستخدم
            $cart = Cart::firstOrCreate([
                'user_id' => $user->id
            ]);


            /**
             * جلب الوحدة
             * مع التأكد أنها تابعة للمنتج
             */
            $unit = ProductUnit::where('id', $data['unit_id'])
                ->where('product_id', $data['product_id'])
                ->firstOrFail();



            // التحقق من المخزون
            $this->checkStock(
                $unit,
                $data['quantity']
            );



            /**
             * البحث هل المنتج موجود مسبقًا
             */
            $cartDetail = CartDetail::where('cart_id', $cart->id)
                ->where('product_id', $data['product_id'])
                ->where('unit_id', $data['unit_id'])
                ->first();



            if ($cartDetail) {


                $newQuantity =
                    $cartDetail->quantity + $data['quantity'];



                $this->checkStock(
                    $unit,
                    $newQuantity
                );


                $cartDetail->update([
                    'quantity' => $newQuantity
                ]);


            } else {


                CartDetail::create([

                    'cart_id' => $cart->id,

                    'product_id' => $data['product_id'],

                    'unit_id' => $data['unit_id'],


                    /*
                     * مهم جدًا:
                     * السعر من قاعدة البيانات
                     * وليس من Request
                     */
                    'price' => $unit->price,


                    'quantity' => $data['quantity']

                ]);

            }



            return $this->loadCart($cart);

        });

    }




    /**
     * تعديل كمية عنصر في السلة
     */
    public function updateQuantity(
        User $user,
        CartDetail $cartDetail,
        int $quantity
    ): Cart {


        $this->verifyOwnership(
            $user,
            $cartDetail
        );



        $unit = $cartDetail->unit;



        $this->checkStock(
            $unit,
            $quantity
        );



        $cartDetail->update([
            'quantity' => $quantity
        ]);



        return $this->loadCart(
            $cartDetail->cart
        );

    }




    /**
     * حذف عنصر من السلة
     */
    public function removeItem(
        User $user,
        CartDetail $cartDetail
    ): void {


        $this->verifyOwnership(
            $user,
            $cartDetail
        );


        $cartDetail->delete();

    }




    /**
     * تفريغ السلة
     */
    public function clearCart(User $user): void
    {

        $cart = Cart::where(
            'user_id',
            $user->id
        )->first();


        if ($cart) {

            $cart->details()->delete();

        }

    }




    /**
     * التحقق من ملكية العنصر
     */
    private function verifyOwnership(
        User $user,
        CartDetail $cartDetail
    ): void {


        if ($cartDetail->cart->user_id !== $user->id) {

            abort(403, 'غير مصرح لك بهذا الإجراء.');

        }

    }




    /**
     * التحقق من توفر المخزون
     */
    private function checkStock(
        ProductUnit $unit,
        int $quantity
    ): void {


        if ($quantity > $unit->stock) {

            abort(
                422,
                'الكمية المطلوبة غير متوفرة في المخزون.'
            );

        }

    }




    /**
     * تحميل بيانات السلة
     */
    private function loadCart(Cart $cart): Cart
    {

        return $cart->load([
            'details.product.images',
            'details.product.category',
            'details.unit'
        ]);

    }

}

