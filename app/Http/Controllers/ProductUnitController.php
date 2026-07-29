<?php

namespace App\Http\Controllers;

use App\Models\ProductUnit;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductUnitController extends Controller
{

    // عرض جميع الوحدات
    public function index()
    {
        $units = ProductUnit::with('product')->get();

        return view('product_units.index', compact('units'));
    }


    // صفحة إضافة وحدة
    public function create()
    {
        $products = Product::all();

        return view('product_units.create', compact('products'));
    }


    // حفظ الوحدة
    public function store(Request $request)
    {
        $request->validate([

            'product_id' => 'required|exists:products,id',

            'unit_name_ar' => 'required',

            'unit_name_en' => 'required',

            'price' => 'required|numeric',

            'stock' => 'nullable|integer',

        ]);


        ProductUnit::create([

            'product_id' => $request->product_id,


            'unit_name' => [

                'ar' => $request->unit_name_ar,

                'en' => $request->unit_name_en,

            ],


            'price' => $request->price,


            'stock' => $request->stock ?? 0,

        ]);


        return redirect()
            ->route('product-units.index')
            ->with('success', 'Unit added successfully');
    }



    // عرض وحدة واحدة
    public function show(ProductUnit $productUnit)
    {
        return view('product_units.show', compact('productUnit'));
    }



    // صفحة تعديل الوحدة
    public function edit(ProductUnit $productUnit)
    {
        $products = Product::all();

        return view('product_units.edit',
            compact('productUnit', 'products'));
    }



    // تحديث الوحدة
    public function update(Request $request, ProductUnit $productUnit)
    {

        $request->validate([

            'product_id' => 'required|exists:products,id',

            'unit_name_ar' => 'required',

            'unit_name_en' => 'required',

            'price' => 'required|numeric',

            'stock' => 'nullable|integer',

        ]);


        $productUnit->update([

            'product_id' => $request->product_id,


            'unit_name' => [

                'ar' => $request->unit_name_ar,

                'en' => $request->unit_name_en,

            ],


            'price' => $request->price,


            'stock' => $request->stock ?? 0,

        ]);


        return redirect()
            ->route('product-units.index')
            ->with('success', 'Unit updated successfully');
    }



    // حذف الوحدة
    public function destroy(ProductUnit $productUnit)
    {

        $productUnit->delete();


        return redirect()
            ->route('product-units.index')
            ->with('success', 'Unit deleted successfully');
    }

}