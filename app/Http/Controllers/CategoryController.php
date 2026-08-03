<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

use App\Http\Resources\CategoryResource;


class CategoryController extends Controller
{

    // عرض جميع التصنيفات
    public function index()
    {
        $categories = Category::all();

        return CategoryResource::collection($categories);
    }



    // حفظ التصنيف
    public function store(StoreCategoryRequest $request)
    {

        $data = $request->validated();


        $imagePath = null;


        if ($request->hasFile('image')) {

            $imagePath = $request
                ->file('image')
                ->store('categories', 'public');

        }



        $category = Category::create([

            'name' => [

                'ar' => $data['name_ar'],

                'en' => $data['name_en'],

            ],


            'slug' => Str::slug($data['name_en']),


            'image' => $imagePath,

        ]);



        return new CategoryResource($category);

    }




    // عرض تصنيف واحد
    public function show(Category $category)
    {

        return new CategoryResource($category);

    }




    // تحديث التصنيف
    public function update(
        UpdateCategoryRequest $request,
        Category $category
    )
    {

        $data = $request->validated();



        $imagePath = $category->image;



        if ($request->hasFile('image')) {


            if (
                $category->image &&
                Storage::disk('public')
                    ->exists($category->image)
            ) {

                Storage::disk('public')
                    ->delete($category->image);

            }



            $imagePath = $request
                ->file('image')
                ->store('categories', 'public');

        }



        $category->update([

            'name' => [

                'ar' => $data['name_ar'],

                'en' => $data['name_en'],

            ],


            'slug' => Str::slug($data['name_en']),


            'image' => $imagePath,

        ]);



        return new CategoryResource($category);

    }





    // حذف التصنيف
    public function destroy(Category $category)
    {


        if (
            $category->image &&
            Storage::disk('public')
                ->exists($category->image)
        ) {

            Storage::disk('public')
                ->delete($category->image);

        }



        $category->delete();



        return response()->json([

            'message' => 'Category deleted successfully'

        ]);

    }

}