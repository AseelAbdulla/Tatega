<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

use App\Http\Resources\ProductResource;


class ProductController extends Controller
{


    // عرض جميع المنتجات
    public function index()
    {

        $products = Product::with('category')
            ->get();


        return ProductResource::collection($products);

    }




    // حفظ المنتج
    public function store(StoreProductRequest $request)
    {

        $data = $request->validated();



        $product = Product::create([


            'category_id' => $data['category_id'],


            'name' => [

                'ar' => $data['name_ar'],

                'en' => $data['name_en'],

            ],




            'sku' => $data['sku'],


            'base_price' => $data['base_price'],


            'has_discount' =>
                $data['has_discount'] ?? false,


            'discount_price' =>
                $data['discount_price'] ?? null,


            'stock' => $data['stock'],


            'low_stock_threshold' =>
                $data['low_stock_threshold'] ?? 5,


            'status' =>
                $data['status'] ?? 'active',

        ]);



        return new ProductResource(
            $product->load('category')
        );

    }




    // عرض منتج واحد
    public function show(Product $product)
    {

        $product->load([
            'category',
            'images',
            'units'
        ]);


        return new ProductResource($product);

    }




    // تحديث المنتج
    public function update(
        UpdateProductRequest $request,
        Product $product
    )
    {


        $data = $request->validated();



        $product->update([


            'category_id' =>
                $data['category_id'],


            'name' => [

                'ar' => $data['name_ar'],

                'en' => $data['name_en'],

            ],


           



            'sku' =>
                $data['sku']
                ?? $product->sku,



            'base_price' =>
                $data['base_price'],



            'has_discount' =>
                $data['has_discount'] ?? false,



            'discount_price' =>
                $data['discount_price'] ?? null,



            'stock' =>
                $data['stock'],



            'low_stock_threshold' =>
                $data['low_stock_threshold'] ?? 5,



            'status' =>
                $data['status'] ?? 'active',

        ]);



        return new ProductResource(
            $product->load('category')
        );

    }




    // حذف المنتج
    public function destroy(Product $product)
    {

        $product->delete();


        return response()->json([

            'message' =>
                'Product deleted successfully'

        ]);

    }

}