<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function createCategory(Request $request)
    {
        $categories = Category::where('parent_id', null)->orderby('name', 'asc')->get();
        if($request->method()=='GET')
        {
            return view('create-category', compact('categories'));
        }
        if($request->method()=='POST')
        {
            $validator = $request->validate([
                'name'      => 'required',
                'category_slug'      => 'required|unique:categories',
                'category_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'parent_id' => 'nullable|numeric'
            ]);

            $categoryImageName = $request->file('category_image');
            $fileName = time().'_'.$categoryImageName->getClientOriginalName();
            $categoryImageName->storeAs('public/images', $fileName);

            Category::create([
                'name' => $request->name,
                'category_slug' => $request->category_slug,
                'category_image' => $fileName,
                'parent_id' =>$request->parent_id
            ]);

            return redirect()->back()->with('success', 'Category has been created successfully.');
        }
    }

    public function getAllCategories()
    {
        $categories = Category::all()->toArray();
        return response()->json($categories);
    }

    public function getParentCategory($categoryId) {
        $currentCategory = Category::findOrFail($categoryId);
        $currentCategoryParentId = $currentCategory->parent_id;
        $parentCategory = Category::find($currentCategoryParentId);
        
        if ($parentCategory) {
            return response()->json(["Current" => $currentCategory, "Parent" => $parentCategory]);
        } else {
            return response()->json(['Current' => $currentCategory]);
        }
    }

    public function categoriesUsedInBlogs()
    {
        // Retrieve all distinct category IDs used in the "blogs" table
        $categoryIds = BLog::distinct()->pluck('category_id');

        // Get the categories based on the retrieved IDs
        $categoriesUsedInBlogs = Category::whereIn('id', $categoryIds)->get();

        return response()->json($categoriesUsedInBlogs);
    }
}
