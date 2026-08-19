<?php
namespace App\Http\Controllers;
use App\Models\ProductUnit;

use App\Http\Requests\StoreProductUnitRequest;
use App\Http\Requests\UpdateProductUnitRequest;

use App\Http\Resources\ProductUnitResource;



class ProductUnitController extends Controller
{


    // عرض جميع الوحدات
    public function index()
    {

        $units = ProductUnit::with('product')
            ->get();


        return ProductUnitResource::collection($units);

    }




    // إضافة وحدة
    public function store(StoreProductUnitRequest $request)
    {

        $data = $request->validated();



        $unit = ProductUnit::create([


            'product_id' => $data['product_id'],


            'unit_name' => [

                'ar' => $data['unit_name_ar'],

                'en' => $data['unit_name_en'],

            ],


            'price' => $data['price'],


            'stock' =>
                $data['stock'] ?? 0,

        ]);



        return new ProductUnitResource(
            $unit->load('product')
        );

    }





    // عرض وحدة واحدة
    public function show(ProductUnit $productUnit)
    {

        return new ProductUnitResource(
            $productUnit->load('product')
        );

    }





    // تحديث الوحدة
    public function update(
        UpdateProductUnitRequest $request,
        ProductUnit $productUnit
    )
    {

        $data = $request->validated();



        $productUnit->update([


            'product_id' =>
                $data['product_id'],


            'unit_name' => [

                'ar' => $data['unit_name_ar'],

                'en' => $data['unit_name_en'],

            ],


            'price' =>
                $data['price'],


            'stock' =>
                $data['stock'] ?? 0,

        ]);



        return new ProductUnitResource(
            $productUnit->load('product')
        );

    }





    // حذف الوحدة
    public function destroy(ProductUnit $productUnit)
    {

        $productUnit->delete();



        return response()->json([

            'message' =>
                'Product unit deleted successfully'

        ]);

    }

}