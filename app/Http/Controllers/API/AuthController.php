<?php

// app/Http/Controllers/API/AuthController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;




class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Email' => 'required|string|email',
            'Password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    

        $user = User::where('Email', $request->Email)->first();
    
        
        if (!$user || !Hash::check($request->Password, $user->Password)) {
            return response()->json(['message' => 'Thông tin đăng nhập không hợp lệ'], 401);
        }
    

        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
        ], 200);
    }

    public function getUserInfo(Request $request)
    {
       
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Người dùng không tìm thấy'], 404);
        }

        return response()->json([
            'user' => $user,
        ], 200);
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
