<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Lấy danh sách tất cả người dùng (bao gồm người dùng đã bị xóa mềm)
    public function index()
    {
        $users = User::withTrashed()->get(); // Lấy cả người dùng đã bị xóa mềm
        return response()->json($users, 200);
    }

    // Tạo mới người dùng
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Username' => 'required|string|max:255',
            'Email' => 'required|string|email|max:255|unique:users',
            'Password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'RoleID' => $request->RoleID ?? 2, // Gán RoleID mặc định
            'Username' => $request->Username,
            'Email' => $request->Email,
            'Password' => Hash::make($request->Password),
        ]);

        return response()->json(['message' => 'Tạo người dùng thành công', 'user' => $user], 201);
    }

    // Cập nhật thông tin người dùng
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'Username' => 'required|string|max:255',
            'Email' => 'required|string|email|max:255|unique:users,Email,' . $user->Email . ',UserID',
            'Password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->Username = $request->Username;
        $user->Email = $request->Email;
        if ($request->filled('Password')) {
            $user->Password = Hash::make($request->Password);
        }

        $user->save();

        return response()->json(['message' => 'Cập nhật người dùng thành công', 'user' => $user], 200);
    }

    // Xóa mềm người dùng
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // Xóa mềm
        return response()->json(['message' => 'Xóa mềm người dùng thành công'], 200);
    }

    // Khôi phục người dùng đã bị xóa mềm
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore(); // Khôi phục lại
        return response()->json(['message' => 'Khôi phục người dùng thành công'], 200);
    }

    // Xóa vĩnh viễn người dùng
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete(); // Xóa vĩnh viễn
        return response()->json(['message' => 'Xóa vĩnh viễn người dùng thành công'], 200);
    }
}

