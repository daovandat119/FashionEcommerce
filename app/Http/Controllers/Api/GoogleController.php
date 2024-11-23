<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::firstOrCreate(
                ['Email' => $googleUser->getEmail()],
                [
                    'RoleID' => 2,
                    'Username' => $googleUser->getName(),
                    'Password' => Hash::make(strtoupper(Str::random(6))),
                    'IsActive' => true,

                ]
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Đăng nhập bằng Google thành công',
                'token' => $token,
                'user' => [
                    'id' => $user->UserID,
                    'email' => $user->Email,
                    'username' => $user->Username,
                    'role_id' => $user->RoleID,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đăng nhập bằng Google thất bại',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
