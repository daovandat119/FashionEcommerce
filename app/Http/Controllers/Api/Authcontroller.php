<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
//
class Authcontroller extends Controller
{
    public function login(AuthRequest $request)
    {
        $user = User::where('Email', $request->Email)->first();

        if (!$user || !Hash::check($request->Password, $user->Password)) {
            throw ValidationException::withMessages([
                'Email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,

        ], 200);
    }


    public function register(RegisterRequest $request)
    {

        $user = User::create([
            'RoleID' => 2,
            'Username' => $request->Username,
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password),
        ]);

        return response()->json([
            'message' => 'Đăng ký thành công',
        ], 201);
    }


    public function getUserInfo(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Người dùng không tìm thấy']);
        }

        return response()->json(['user' => [
            'RoleID' => $user->RoleID,
            'UserID' => $user->UserID,
            'Username' => $user->Username,
            'Email' => $user->Email,

        ]], 200);
    }
}
