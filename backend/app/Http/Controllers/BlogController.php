<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Tymon\JWTAuth\Exceptions\JWTException;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getLatestBlogs', 'getBlogDetails', 'getRelatedBlogs', 'getBlogsByCategoryId']]); //login, register methods won't go through the api guard
    }

    public function createBlog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:2|max:100',
            'blog_title_slug' => 'required|string|min:2|max:100',
            'short_description' => 'required|string|min:10|max:250',
            'long_description' => 'required|string|min:10|max:2000',
            'blog_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'blog_video_link' => 'required|string|max:200',
            'category_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = auth()->user();

        $blogImageName = $request->file('blog_image');
        $fileName = time() . '_' . $blogImageName->getClientOriginalName();
        $blogImageName->storeAs('public/images', $fileName);

        $blog = Blog::create([
            'title' => $request->title,
            'blog_title_slug' => $request->blog_title_slug,
            'short_description' => $request->short_description,
            'long_description' => $request->long_description,
            'blog_video_link' => $request->blog_video_link,
            'blog_image' => $fileName,
            'user_id' => $user->id,
            'category_id' => $request->category_id,
        ]);

        $review = Review::create([
            'blog_id' => $blog->id,
            'review_data' => null, // You can set it to null initially
        ]);

        return response()->json([
            'message' => 'Successfully created !',
            'Blog' => $blog
        ], 201);
    }

    public function editBlog(Request $request, $id)
    {
        // try{
        //     if (!$user = JWTAuth::parseToken()->authenticate()) {
        //         return response()->json(['message' => 'User not found'], 404);
        //     }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:2|max:50',
            'blog_title_slug' => 'required|string|min:10|max:500',
            'short_description' => 'required|string|min:10|max:500',
            'long_description' => 'required|string|max:2000',
            'blog_image' => 'required',
            'blog_video_link' => 'required|string|max:2000',
            'category_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $blog = Blog::find($id);

        if (!$blog) {
            return response()->json(['message' => 'blog not found'], 404);
        }

        $blog->fill($request->all());

        if ($request->hasFile('blog_image')) {
            $blogImageName = $request->file('blog_image');
            $fileName = time() . '_' . $blogImageName->getClientOriginalName();
            $blogImageName->storeAs('public/images', $fileName);
            $blog->blog_image = $fileName;
        }
        $blog->save();
        return response()->json(['message' => 'blog updated successfully', 'blog' => $blog]);
    }

    public function deleteBlog($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'blog not found'], 404);
        }
        $blog->delete();
        return response()->json(['message' => 'blog deleted successfully'], 200);
    }

    public function getLatestBlogs()
    {
        $latestBlogs = Blog::with('user')->latest()->take(5)->where('is_active', '!=', 0)->get();;
        return response()->json($latestBlogs);
    }

    public function getBlogsByCategoryId($categoryId)
    {
        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $blogs = Blog::with('user')->where('category_id', $categoryId)->where('is_active', '!=', 0)->get();

        return response()->json($blogs);
    }

    public function getBlogDetails($blogId)
    {
        $blog = Blog::with('user')->with('category')->find($blogId);
        if (!$blog) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($blog);
    }

    public function getRelatedBlogs($blogId)
    {
        $category_id = Blog::find($blogId)->category_id;

        $relatedBlogs = Blog::where('category_id', $category_id)
            ->where('id', '!=', $blogId) // Exclude the current blog
            ->orderBy('created_at', 'desc') // You can adjust the sorting as needed
            // ->limit(5) // Limit the results to the latest 5 related blogs
            ->get();

        return response()->json($relatedBlogs);
    }

    public function getUserBlogs() {
        $user = auth()->user(); 
        $blogs = Blog::with('user')->where('user_id', $user->id)->get();
        return response()->json($blogs);
    }
}
