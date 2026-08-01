<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
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
            ->get();


        return ProductImageResource::collection($images);

    }




    // إضافة صورة
    public function store(StoreProductImageRequest $request)
    {

        $data = $request->validated();



        $imagePath = $request
            ->file('image')
            ->store('products','public');



        $image = ProductImage::create([


            'product_id' => $data['product_id'],


            'image_path' => $imagePath,


            'is_main' =>
                $data['is_main'] ?? false,


            'sort_order' =>
                $data['sort_order'] ?? 0,

        ]);



        return new ProductImageResource(
            $image->load('product')
        );

    }





    // عرض صورة
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
    )
    {

        $data = $request->validated();



        if($request->hasFile('image')){


            if(
                $productImage->image_path &&
                Storage::disk('public')
                ->exists($productImage->image_path)
            ){

                Storage::disk('public')
                ->delete($productImage->image_path);

            }



            $data['image_path'] =
                $request->file('image')
                ->store('products','public');

        }



        $productImage->update([

            'product_id' => $data['product_id'],

            'image_path' =>
                $data['image_path']
                ?? $productImage->image_path,

            'is_main' =>
                $data['is_main'] ?? false,


            'sort_order' =>
                $data['sort_order'] ?? 0,

        ]);



        return new ProductImageResource(
            $productImage->load('product')
        );

    }





    // حذف الصورة
    public function destroy(ProductImage $productImage)
    {


        if(
            $productImage->image_path &&
            Storage::disk('public')
            ->exists($productImage->image_path)
        ){

            Storage::disk('public')
            ->delete($productImage->image_path);

        }



        $productImage->delete();



        return response()->json([

            'message'=>'Product image deleted successfully'

        ]);

    }

}