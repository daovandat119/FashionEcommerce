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

    public function restore($id)
    {
        $user = User::findOrFail($id);
        $user->IsActive = true;
        $user->save();

        return response()->json(['message' => 'Người dùng đã được kích hoạt lại']);
    }
}
