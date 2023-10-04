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
        $categories = Category::all()->pluck('name','id')->toArray();
        return response()->json($categories);
    }

    // public function getRelatedPosts($postId)
    // {
    //     // Get the category ID of the current post
    //     $recipe = Blog::find($postId);
    //     $categoryId = $recipe->category_id;

    //     // Get related posts under the same category
    //     $relatedPosts = Blog::where('category_id', $categoryId)
    //                         ->where('id', '!=', $postId) // Exclude the current post itself
    //                         ->get();

    //     return response()->json($relatedPosts);
    //     // You can also pass the related posts to a view and display them as needed.
    // }
}