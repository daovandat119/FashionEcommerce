<?php

// app/Http/Controllers/API/AuthController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\VerifyAccount;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;


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

        $credentials = $request->only('Email', 'Password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Thông tin đăng nhập không hợp lệ'], 401);
        }

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
        ], 200);
    }

    public function getUserFromToken()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'Người dùng không tìm thấy'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Token không hợp lệ hoặc hết hạn'], 401);
        }

        return response()->json(['user' => $user], 200);
    }



    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Username' => 'required|string|max:255',
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
            'RoleID' => 2,
            'Username' => $request->Username,
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password),
            'Image' => $imagePath,
        ]);


        return response()->json(['message' => 'Đăng ký thành công, vui lòng kiểm tra email để xác nhận tài khoản của bạn.'], 201);
    }

    public function testEmail()
    {
        try {
            Mail::raw('Đây là một email test.', function ($message) {
                $message->to('tranductruong2k4hb@gmail.com')
                    ->subject('Test Email');
            });
            return 'Email đã được gửi!';
        } catch (Exception $e) {
            return 'Có lỗi xảy ra: ' . $e->getMessage();
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}