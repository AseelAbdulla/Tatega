<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{

    // عرض الصور
    public function index()
    {
        $images = ProductImage::with('product')->get();

        return view('product_images.index', compact('images'));
    }


    // صفحة إضافة صورة
    public function create()
    {
        $products = Product::all();

        return view('product_images.create', compact('products'));
    }


    // حفظ الصورة
    public function store(Request $request)
    {

        $request->validate([

            'product_id' => 'required',

            'image' => 'required|image',

        ]);


        $imagePath = $request->file('image')
            ->store('products', 'public');


        ProductImage::create([

            'product_id' => $request->product_id,

            'image' => $imagePath,

            'is_main' => $request->is_main ?? false,

            'sort_order' => $request->sort_order ?? 0,

        ]);


        return redirect()
            ->route('product-images.index')
            ->with('success','Image added successfully');

    }



    // عرض صورة
    public function show(ProductImage $productImage)
    {
        return view('product_images.show', compact('productImage'));
    }



    // تعديل
    public function edit(ProductImage $productImage)
    {
        $products = Product::all();

        return view('product_images.edit',
            compact('productImage','products'));
    }



    // تحديث
    public function update(Request $request, ProductImage $productImage)
    {

        $data = [

            'product_id'=>$request->product_id,

            'is_main'=>$request->is_main ?? false,

            'sort_order'=>$request->sort_order ?? 0,

        ];


        if($request->hasFile('image')){

            $data['image'] =
                $request->file('image')
                ->store('products','public');

        }


        $productImage->update($data);


        return redirect()
            ->route('product-images.index');

    }



    // حذف
    public function destroy(ProductImage $productImage)
    {

        $productImage->delete();


        return redirect()
            ->route('product-images.index');

    }

}