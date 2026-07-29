<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    // عرض جميع التصنيفات
    public function index()
    {
        $categories = Category::all();

        return view('categories.index', compact('categories'));
    }


    // صفحة إضافة تصنيف
    public function create()
    {
        return view('categories.create');
    }


    // حفظ التصنيف
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image',
        ]);


        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('categories', 'public');
        }


        Category::create([
            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],

            'slug' => Str::slug($request->name_en),

            'image' => $imagePath,
        ]);


        return redirect()
            ->route('categories.index')
            ->with('success', 'Category added successfully');
    }



    // عرض تصنيف واحد
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }



    // صفحة التعديل
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }



    // تحديث التصنيف
    public function update(Request $request, Category $category)
    {

        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image',
        ]);


        $imagePath = $category->image;


        if ($request->hasFile('image')) {

            $imagePath = $request->file('image')
                ->store('categories', 'public');
        }


        $category->update([

            'name' => [
                'ar' => $request->name_ar,
                'en' => $request->name_en,
            ],

            'slug' => Str::slug($request->name_en),

            'image' => $imagePath,

        ]);


        return redirect()
            ->route('categories.index');
    }



    // حذف التصنيف
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('categories.index');
    }
}