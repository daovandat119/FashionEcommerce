<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {
            
            $user = Auth::user();
            
            // Kiểm tra xem vai trò của người dùng có phải là Admin không
            if ($user->role && $user->role->RoleName === 'Admin') {
                return $next($request); 
            }
        }

        abort(403, 'Unauthorized action.');
    }

    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.admin' => \App\Http\Middleware\AdminMiddleware::class, // Thêm dòng này
    ];

    
}
