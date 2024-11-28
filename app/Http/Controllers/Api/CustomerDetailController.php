<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Users;
use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Facades\DB;

class CustomerDetailController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'message' => 'Thông tin tài khoản',
            'data' => [
                'id' => $user->UserID,
                'username' => $user->Username,
                'email' => $user->Email,
                'is_active' => $user->IsActive,
                'image' => $user->Image,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        if (!Hash::check($request->input('current_password'), $user->Password)) {
            return response()->json([
                'message' => 'Mật khẩu hiện tại không chính xác.'
            ], 400);
        }

        if($request->input('new_password') !== $request->input('new_password_confirmation')) {
            return response()->json([
                'message' => 'Mật khẩu không khớp.'
            ], 400);
        }

        $user->Password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json([
            'message' => 'Mật khẩu đã được thay đổi thành công.'
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $userId = auth()->id();

        $userUpdate = [
            'Username' => $request->name,
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $uploadedFileUrl = (new UploadApi())->upload($request->file('image')->getRealPath())['secure_url'];
            $userUpdate['image'] = $uploadedFileUrl;
        }

        DB::table('users')->where('UserID', $userId)->update($userUpdate);

        return response()->json([
            'message' => 'Thông tin tài khoản đã được cập nhật thành công.',
        ], 200);

    }
}

