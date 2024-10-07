<?php

// app/Http/Controllers/API/AuthController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Email' => 'required|string|email',
            'Password' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $user = User::where('Email', $request->Email)->first();
    
        if (!$user || !Hash::check($request->Password, $user->Password)) {
            return response()->json(['message' => 'Thông tin đăng nhập không hợp lệ'], 401);
        }
    
        // Tạo token cho người dùng
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // Kiểm tra role của người dùng và trả về thông báo tương ứng
        if ($user->role->RoleName === 'Admin') {
            return response()->json(['message' => 'Đăng nhập thành công trang quản trị', 'token' => $token]);
        } else {
            return response()->json(['message' => 'Đăng nhập thành công trang người dùng', 'token' => $token]);
        }
    }
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Username' => 'required|string|max:255|unique:users',
            'Email' => 'required|string|email|max:255|unique:users',
            'Password' => 'required|string|min:6',
            'Image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        $imagePath = null;
        if ($request->hasFile('Image')) {
            $image = $request->file('Image');
            $imagePath = $image->store('images', 'public');
        }
    
        $user = User::create([
            'RoleID' => 2, // Gán RoleID mặc định cho user mới là 2 (User)
            'Username' => $request->Username,
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password),
            'Image' => $imagePath,
        ]);
    
        return response()->json(['message' => 'Đăng ký thành công, người dùng đã được tạo.'], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Đã đăng xuất thành công']);
    }
}

