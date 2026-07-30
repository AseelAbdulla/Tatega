<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    // عرض المنتجات
    public function index()
    {
        $products = Product::with('category')->get();

        return view('products.index', compact('products'));
    }


    // صفحة إضافة منتج
    public function create()
    {
        $categories = Category::all();

        return view('products.create', compact('categories'));
    }


    // حفظ المنتج
    public function store(Request $request)
    {

        $request->validate([

            'category_id' => 'required',

            'name_ar' => 'required',
            'name_en' => 'required',

            'base_price' => 'required|numeric',

            'sku' => 'required|unique:products',

        ]);


        Product::create([

            'category_id' => $request->category_id,

            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],

            'slug' => Str::slug($request->name_en),

            'sku' => $request->sku,

            'base_price' => $request->base_price,

            'has_discount' => $request->has_discount ?? false,

            'discount' => $request->discount,

            'stock' => $request->stock,

            'low_stock_threshold' => $request->low_stock_threshold,

            'status' => $request->status ?? 'active',

        ]);


        return redirect()
            ->route('products.index')
            ->with('success','Product added successfully');

    }



    // عرض منتج
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }



    // صفحة تعديل
    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('products.edit', compact('product','categories'));
    }



    // تحديث المنتج
    public function update(Request $request, Product $product)
    {

        $product->update([

            'category_id' => $request->category_id,

            'name' => [
                'ar'=>$request->name_ar,
                'en'=>$request->name_en,
            ],

            'base_price'=>$request->base_price,

            'has_discount'=>$request->has_discount ?? false,

            'discount'=>$request->discount,

            'stock'=>$request->stock,

            'status'=>$request->status,

        ]);


        return redirect()
            ->route('products.index');

    }



    // حذف المنتج
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index');
    }

}