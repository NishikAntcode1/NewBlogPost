<?php

namespace App\Http\Controllers;


use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use JWTAuth;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]); //login, register methods won't go through the api guard
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|min:6',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 402);
        }

        if (!$token = auth('api')->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $user = auth('api')->user();

        return $this->respondWithToken($token, $user);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function getaccount()
    {
        $user = auth()->user()->load('blogs');

        return response()->json($user);
    }


    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    // public function refresh()
    // {
    //     return $this->respondWithToken(auth()->refresh());
    // }
    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL(), //mention the guard name inside the auth fn
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        // return $request;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:50',
            'about' => 'required|string|min:10',
            'age' => 'required|integer|min:10',
            'profile_image' => 'required',
            'address' => 'required|string',
            'gender' => 'required|string',
            'phone' => 'required|numeric|digits:10',
            'profession' => 'required|string|min:2|max:50',
            'qualification' => 'required|string|min:2|max:50'
            
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user->update([
            'name' => $request->input('name'),
            'about' => $request->input('about'),
            'age' => $request->input('age'),
            'address' => $request->input('address'),
            'gender' => $request->input('gender'),
            'phone' => $request->input('phone'),
            'profession' => $request->input('profession'),
            'qualification' => $request->input('qualification'),
        ]);

        if ($request->hasFile('profile_image')) {
            $profileImageName = $request->file('profile_image');
            $fileName = time() . '_' . $profileImageName->getClientOriginalName();
            $profileImageName->storeAs('public/images', $fileName);
            $user->profile_image = $fileName;
        }


        $user->save();

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }
}
