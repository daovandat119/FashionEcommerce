<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\NewPassword;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Mail\NewPasswordEmail;

class AuthController extends Controller
{
    public function login(AuthRequest $request)
    {
        $user = User::where('Email', $request->Email)->first();

        if (!$user || !Hash::check($request->Password, $user->Password)) {
            throw ValidationException::withMessages([
                'Email' => ['Tài khoản hoặc mật khẩu không chính xác.'],
            ]);
        }

        if (!$user->IsActive) {
            return response()->json(['message' => 'Tài khoản chưa được xác minh. Vui lòng kiểm tra email để xác minh tài khoản.'], 403);
        }



        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',

            'token' => $token,


        ], 200);
    }

    public function loginAdmin(AuthRequest $request)
    {

        $user = User::where('Email', $request->Email)->first();


        if (!$user || !Hash::check($request->Password, $user->Password)) {
            throw ValidationException::withMessages([
                'Email' => ['Tài khoản hoặc mật khẩu không chính xác.'],
            ]);
        }


        if (!$user->IsActive) {
            return response()->json(['message' => 'Tài khoản chưa được xác minh. Vui lòng kiểm tra email để xác minh tài khoản.'], 403);
        }


        if ($user->RoleID !== 1) {
            return response()->json(['message' => 'Bạn không có quyền truy cập admin.'], 403);
        }


        $token = $user->createToken('admin_auth_token', ['admin'])->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập admin thành công',
            'token' => $token,
            'isAdmin' => true,
        ], 200);
    }


    public function register(RegisterRequest $request)
    {

        $verificationCode = strtoupper(Str::random(6));
        $user = User::create([
            'RoleID' => 2,
            'Username' => $request->Username,
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password),
            'IsActive' => false,
            'CodeId' => $verificationCode,
            'CodeExpired' => now()->addMinutes(5),
        ]);

        Mail::to($user->Email)->send(new VerifyEmail($user, $verificationCode));

        return response()->json([
            'message' => 'Đăng ký thành công, vui lòng kiểm tra email để xác minh tài khoản.',
        ], 201);
    }

    public function verify(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->CodeExpired && now()->greaterThan($user->CodeExpired)) {
            return response()->json(['message' => 'Mã xác minh đã hết hạn. Vui lòng đăng ký lại.'], 400);
        }


        if ($request->input('CodeId') !== $user->CodeId) {
            return response()->json(['message' => 'Mã xác minh không chính xác.'], 400);
        }

        $user->CodeId = null;
        $user->CodeExpired = null;
        $user->IsActive = true;
        $user->save();

        return response()->json(['message' => 'Tài khoản đã được xác minh thành công!'], 200);
    }

    public function resendVerificationCode(Request $request)
    {
        $user = User::where('Email', $request->Email)->first();

        if (!$user || $user->IsActive) {
            return response()->json(['message' => 'Tài khoản không tồn tại hoặc đã được xác minh.'], 400);
        }

        if ($user->CodeId && $user->CodeExpired > now()) {
            return response()->json([
                'message' => 'Mã xác minh hiện tại vẫn còn hiệu lực. Vui lòng kiểm tra email của bạn.'
            ], 400);
        }

        $newVerificationCode = strtoupper(Str::random(6));
        $user->CodeId = $newVerificationCode;
        $user->CodeExpired = now()->addMinutes(5);
        $user->save();


        Mail::to($user->Email)->send(new VerifyEmail($user, $newVerificationCode));

        return response()->json([
            'message' => 'Mã xác minh mới đã được gửi. Vui lòng kiểm tra email để xác minh tài khoản.',
        ], 200);
    }

    public function forgotPassword(Request $request)
    {

        $user = User::where('Email', $request->Email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email không tồn tại trong hệ thống.'], 404);
        }

        if ($user->CodeExpired && $user->CodeExpired->gt(now()->subDay())) {
            return response()->json([
                'message' => 'Bạn chỉ có thể đổi mật khẩu sau 24 giờ kể từ lần đổi gần nhất.'
            ], 400);
        }

        $newPassword = Str::random(6);


        $user->Password = Hash::make($newPassword);
        $user->save();

        Mail::to($user->Email)->send(new NewPassword($user, $newPassword));

        return response()->json([
            'message' => 'Mật khẩu mới đã được gửi đến email của bạn.',
        ], 200);
    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công',
        ], 200);
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
