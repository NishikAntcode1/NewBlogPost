<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::post('/createBlog', [BlogController::class, 'createBlog']);
Route::post('/editBlog/{id}', [BlogController::class, 'editBlog']);
Route::post('/deleteBlog/{id}', [BlogController::class, 'deleteBlog']);
Route::post('/getLatestBlogs', [BlogController::class, 'getLatestBlogs']);
Route::get('/getBlogsByCategoryId/{categoryId}', [BlogController::class, 'getBlogsByCategoryId']);
Route::get('/getBlogDetails/{blogId}', [BlogController::class, 'getBlogDetails']);
Route::get('/getRelatedBlogs/{blogId}', [BlogController::class, 'getRelatedBlogs']);


Route::post('/getAllCategories', [CategoryController::class, 'getAllCategories']);
Route::get('/categoriesUsedInBlogs', [CategoryController::class, 'categoriesUsedInBlogs']);
Route::get('/getParentCategory/{categoryId}', [CategoryController::class, 'getParentCategory']);

Route::middleware(['api'])->group(function() {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/getaccount', [AuthController::class, 'getaccount']);
});