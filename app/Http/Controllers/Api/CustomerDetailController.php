<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class CustomerDetailController extends Controller
{
    public function show(Request $request)
    {
        // Lấy thông tin người dùng hiện tại
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
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        $user = Auth::user();
        if (!Hash::check($request->input('current_password'), $user->Password)) {
            return response()->json([
                'message' => 'Mật khẩu hiện tại không chính xác.'
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
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'name' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();
        if ($request->has('name')) {
            $user->Username = $request->name;
        }
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads', 'public');
            $user->Image = $imagePath;
        }

        // Lưu thông tin mới
        $user->save();

        return response()->json([
            'message' => 'Thông tin tài khoản đã được cập nhật thành công.',
            'data' => [
                'name' => $user->Username,
                'image' => asset('storage/' . $user->Image),
            ],
        ], 200);
    }
}

