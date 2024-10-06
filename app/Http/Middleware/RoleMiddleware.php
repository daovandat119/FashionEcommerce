<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Kiểm tra người dùng đã đăng nhập và vai trò của người dùng
        if (auth()->check() && auth()->user()->role->RoleName == $role) {
            return $next($request);
        }

        return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
    }
}
