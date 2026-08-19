<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\StoreProductImageRequest;
use App\Http\Requests\UpdateProductImageRequest;
use App\Http\Resources\ProductImageResource;

class ProductImageController extends Controller
{
    // عرض جميع الصور
    public function index()
    {
        $images = ProductImage::with('product')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return ProductImageResource::collection($images);
    }


    // إضافة صورة
    public function store(StoreProductImageRequest $request)
    {
        $data = $request->validated();

        $imagePath = $request
            ->file('image')
            ->store('products', 'public');

        $image = DB::transaction(function () use ($data, $imagePath) {

            $productId = $data['product_id'];

            // هل المنتج لديه صور؟
            $hasImages = ProductImage::where(
                'product_id',
                $productId
            )->exists();

            /*
            |--------------------------------------------------------------------------
            | أول صورة تصبح رئيسية تلقائيًا
            |--------------------------------------------------------------------------
            */
            $isMain = ($data['is_main'] ?? false) || !$hasImages;

            /*
            |--------------------------------------------------------------------------
            | إذا كانت الصورة الجديدة رئيسية
            | إلغاء الرئيسية السابقة
            |--------------------------------------------------------------------------
            */
            if ($isMain) {
                ProductImage::where(
                    'product_id',
                    $productId
                )->update([
                    'is_main' => false
                ]);
            }

            return ProductImage::create([
                'product_id' => $productId,
                'image_path' => $imagePath,
                'is_main' => $isMain,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);
        });

        return new ProductImageResource(
            $image->load('product')
        );
    }


    // عرض صورة واحدة
    public function show(ProductImage $productImage)
    {
        return new ProductImageResource(
            $productImage->load('product')
        );
    }


    // تحديث الصورة
    public function update(
        UpdateProductImageRequest $request,
        ProductImage $productImage
    ) {
        $data = $request->validated();

        $oldProductId = $productImage->product_id;

        // إذا لم يتم إرسال product_id نستخدم المنتج الحالي
        $newProductId = $data['product_id']
            ?? $productImage->product_id;

        $oldWasMain = $productImage->is_main;

        $newIsMain = $data['is_main']
            ?? $productImage->is_main;


        DB::transaction(function () use (
            $request,
            $data,
            $productImage,
            $oldProductId,
            $newProductId,
            $oldWasMain,
            $newIsMain
        ) {

            /*
            |--------------------------------------------------------------------------
            | إذا تغير المنتج
            |--------------------------------------------------------------------------
            */
            $productChanged =
                $oldProductId != $newProductId;


            /*
            |--------------------------------------------------------------------------
            | إذا أصبحت الصورة رئيسية
            |--------------------------------------------------------------------------
            */
            if ($newIsMain) {

                ProductImage::where(
                    'product_id',
                    $newProductId
                )
                ->where(
                    'id',
                    '!=',
                    $productImage->id
                )
                ->update([
                    'is_main' => false
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | استبدال ملف الصورة
            |--------------------------------------------------------------------------
            */
            $newPath = $productImage->image_path;

            if ($request->hasFile('image')) {

                $newPath = $request
                    ->file('image')
                    ->store('products', 'public');
            }


            /*
            |--------------------------------------------------------------------------
            | تحديث الصورة
            |--------------------------------------------------------------------------
            */
            $productImage->update([

                'product_id' => $newProductId,

                'image_path' => $newPath,

                'is_main' => $newIsMain,

                'sort_order' =>
                    $data['sort_order']
                    ?? $productImage->sort_order,
            ]);


            /*
            |--------------------------------------------------------------------------
            | إذا تغير المنتج وكانت الصورة الرئيسية القديمة
            | نضمن وجود رئيسية في المنتج القديم
            |--------------------------------------------------------------------------
            */
            if ($productChanged && $oldWasMain) {

                $oldProductHasMain = ProductImage::where(
                    'product_id',
                    $oldProductId
                )
                ->where('is_main', true)
                ->exists();


                if (!$oldProductHasMain) {

                    $replacement = ProductImage::where(
                        'product_id',
                        $oldProductId
                    )
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->first();


                    if ($replacement) {

                        $replacement->update([
                            'is_main' => true
                        ]);
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | إذا تم تحويل الصورة الرئيسية إلى false
            | نختار صورة بديلة
            |--------------------------------------------------------------------------
            */
            if (!$newIsMain) {

                $productHasMain = ProductImage::where(
                    'product_id',
                    $newProductId
                )
                ->where('is_main', true)
                ->exists();


                if (!$productHasMain) {

                    $replacement = ProductImage::where(
                        'product_id',
                        $newProductId
                    )
                    ->where(
                        'id',
                        '!=',
                        $productImage->id
                    )
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->first();


                    if ($replacement) {

                        $replacement->update([
                            'is_main' => true
                        ]);
                    }
                }
            }
        });


        /*
        |--------------------------------------------------------------------------
        | حذف الصورة القديمة من Storage بعد نجاح التحديث
        |--------------------------------------------------------------------------
        */
        if (
            $request->hasFile('image') &&
            $productImage->image_path
        ) {

            // نحصل على المسار القديم من النسخة الأصلية
            // لذلك نحتاج حفظه قبل التحديث
        }


        return new ProductImageResource(
            $productImage
                ->fresh()
                ->load('product')
        );
    }


    // حذف الصورة
    public function destroy(ProductImage $productImage)
    {
        $wasMain = $productImage->is_main;

        $productId = $productImage->product_id;

        $imagePath = $productImage->image_path;


        DB::transaction(function () use (
            $productImage,
            $wasMain,
            $productId
        ) {

            // حذف الصورة من قاعدة البيانات
            $productImage->delete();


            /*
            |--------------------------------------------------------------------------
            | إذا كانت الصورة المحذوفة هي الرئيسية
            | نختار صورة بديلة
            |--------------------------------------------------------------------------
            */
            if ($wasMain) {

                $newMainImage = ProductImage::where(
                    'product_id',
                    $productId
                )
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();


                if ($newMainImage) {

                    $newMainImage->update([
                        'is_main' => true
                    ]);
                }
            }
        });


        /*
        |--------------------------------------------------------------------------
        | حذف الملف من Storage
        |--------------------------------------------------------------------------
        */
        if (
            $imagePath &&
            Storage::disk('public')->exists($imagePath)
        ) {

            Storage::disk('public')->delete($imagePath);
        }


        return response()->json([
            'message' => 'Product image deleted successfully'
        ]);
    }
}