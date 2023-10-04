<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Tymon\JWTAuth\Exceptions\JWTException;

class BlogController extends Controller
{
    //
    public function createBlog(Request $request) {
        // try {
            // Get the authenticated user using JWT token
            // if (!$user = JWTAuth::parseToken()->authenticate()) {
            //     return response()->json(['message' => 'User not found'], 404);
            // }
            $validator = Validator::make($request ->all(), [
                'title' => 'required|string|min:2|max:100',
                'blog_title_slug' => 'required|string|min:2|max:100',
                'short_description' => 'required|string|min:10|max:200',
                'long_description' => 'required|string|min:10|max:2000',
                'blog_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'blog_video_link' => 'required|string|max:200',
                'category_id'=> 'required'
            ]);
            if($validator->fails())
            {
                return response()->json($validator->errors(),400);
            }

            $blogImageName = $request->file('blog_image');
            $fileName = time().'_'.$blogImageName->getClientOriginalName();
            $blogImageName->storeAs('public/images', $fileName);

            $blog = Blog::create([
                'title' => $request -> title,
                'blog_title_slug' => $request -> blog_title_slug,
                'short_description' => $request -> short_description,
                'long_description' => $request -> long_description,
                'blog_video_link' => $request -> blog_video_link,
                'blog_image' => $fileName,
                // 'user_id' => $user->id,
                'category_id' => $request->category_id,
            ]);

            return response()->json([
                'message' => 'Successfully created !',
                'Blog' => $blog
            ], 201);
        // }
        // catch (JWTException $e) {
        //     return response()->json(['message' => 'Failed to authenticate token'], 500);
        // }
    }

    public function editBlog(Request $request, $id){
        // try{
        //     if (!$user = JWTAuth::parseToken()->authenticate()) {
        //         return response()->json(['message' => 'User not found'], 404);
        //     }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|min:2|max:50',
                'blog_title_slug' => 'required|string|min:10|max:500',
                'short_description' => 'required|string|min:10|max:500',
                'long_description' => 'required|string|max:2000',
                'blog_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'blog_video_link' => 'required|string|max:2000',
                'category_id'=> 'required'
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
                $fileName = time().'_'.$blogImageName->getClientOriginalName();
                $blogImageName->storeAs('public/images', $fileName);
                $blog->blog_image = $fileName;
            }
            $blog->save();
            return response()->json(['message' => 'blog updated successfully', 'blog' => $blog]);

        // }
        // catch (JWTException $e) {
        //     return response()->json(['message' => 'Failed to authenticate token'], 500);
        // }
    }

    public function deleteBlog($id)
    {
        // try{
        //     if (!$user = JWTAuth::parseToken()->authenticate()) {
        //         return response()->json(['message' => 'User not found'], 404);
        //     }

            $blog = Blog::find($id);
            if (!$blog) {
                return response()->json(['message' => 'blog not found'], 404);
            }
            $blog->delete();
            return response()->json(['message' => 'blog deleted successfully'], 200);
        // }
        // catch (JWTException $e) {
        //     return response()->json(['message' => 'Failed to authenticate token'], 500);
        // }
    }
}
