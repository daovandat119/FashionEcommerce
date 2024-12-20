<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $total = User::join('roles', 'users.RoleID', '=', 'roles.RoleID')
            ->when($request->search, function ($query) use ($request) {
                $query->where('users.Username', 'like', "%{$request->search}%");
            })
            ->where('roles.RoleID', 2)
            ->count();
        $page = $request->input('Page', 1);
        $limit = $request->input(
            'Limit',
            10
        );
        $offset = ($page - 1) * $limit;

        $users = User::skip($offset)
            ->join('roles', 'users.RoleID', '=', 'roles.RoleID')
            ->when($request->search, function ($query) use ($request) {
                $query->where('users.Username', 'like', "%{$request->search}%");
            })
            ->where('roles.RoleID', 2)
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

    public function restore($id)
    {
        $user = User::findOrFail($id);
        $user->IsActive = true;
        $user->save();

        return response()->json(['message' => 'Người dùng đã được kích hoạt lại']);
    }

    public function showUser(Request $request)
    {
        $userId = auth()->id();

        // Fetch the user details by joining with the roles table
        $user = User::join('addresses', 'users.UserID', '=', 'addresses.UserID') // Join with addresses table
            ->select('users.*', 'addresses.*') // Select all user and address fields
            ->where('users.UserID', $userId)
            ->first(); // Get the first result

        // Check if user exists
        if (!$user) {
            return response()->json([
                'message' => 'Người dùng không tồn tại.'
            ], 404);
        }

        return response()->json([
            'message' => 'Thông tin tài khoản',
            'data' => [
                'id' => $user->UserID,
                'username' => $user->Username,
                'email' => $user->Email,
                'is_active' => $user->IsActive,
                'image' => $user->Image,
                'ProvinceID' => $user->ProvinceID,
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

        if ($request->input('new_password') !== $request->input('new_password_confirmation')) {
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

    public function updateProfile(UserRequest $request)
    {
        $userId = auth()->id();

        $userUpdate = [
            'Username' => $request->name,
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $uploadedFileUrl = (new UploadApi())->upload($request->file('image')->getRealPath())['secure_url'];
            $userUpdate['image'] = $uploadedFileUrl;
        } else {
            $user = User::find($userId);
            $userUpdate['image'] = $user->Image;
        }

        DB::table('users')->where('UserID', $userId)->update($userUpdate);

        return response()->json([
            'message' => 'Thông tin tài khoản đã được cập nhật thành công.',
        ], 200);
    }
}
