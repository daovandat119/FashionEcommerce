<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('IsActive', true)->get();
        return response()->json($users);
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
