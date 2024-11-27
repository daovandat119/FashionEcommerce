<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $total = User::count();
        $page = $request->input('Page', 1);
        $limit = $request->input('Limit', 10);
        $offset = ($page - 1) * $limit;


        $users = User::skip($offset)
            ->take($limit)
            ->get();

        $totalPage = ceil($total / $limit);

        return response()->json([
            'message' => 'Success',
            'data' => $users,
            'total' => $total,
            'totalPage' => $totalPage,
            'page' => $page,
            'limit' => $limit
        ], 200);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
    //
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->IsActive = false;
        $user->save();

        return response()->json(['message' => 'Người dùng đã bị vô hiệu hóa']);
    }
//
//
    public function restore($id)
    {
        $user = User::findOrFail($id);
        $user->IsActive = true;
        $user->save();

        return response()->json(['message' => 'Người dùng đã được kích hoạt lại']);
    }
    public function store(Request $request)
{
    // Xác thực dữ liệu đầu vào
    $validatedData = $request->validate([
        'Username' => 'required|string|max:255|unique:users,Username',
        'Email' => 'required|email|unique:users,Email',
        'Password' => 'required|string|min:8',
        'Image' => 'nullable|string',
        'RoleID' => 'required|integer',
    ]);

    // Tạo người dùng mới
    $user = new User();
    $user->Username = $validatedData['Username'];
    $user->Email = $validatedData['Email'];
    $user->Password = bcrypt($validatedData['Password']);
    $user->Image = $validatedData['Image'] ?? null;
    $user->RoleID = $validatedData['RoleID'];
    $user->IsActive = true; // Mặc định kích hoạt
    $user->save();

    // Trả về phản hồi
    return response()->json([
        'message' => 'Người dùng mới đã được thêm thành công',
        'data' => $user
    ], 201);
}

}
