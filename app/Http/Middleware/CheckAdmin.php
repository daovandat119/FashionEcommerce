<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {

            $user = Auth::user();

            if ($user->role && $user->role->RoleName === 'Admin') {
                \Log::info('Admin access granted to user ID: ' . $user->UserID);
                return $next($request);
            }
        }
        \Log::warning('Access denied for non-admin user');
        abort(403, 'Không đủ quyền');

    }

 
}
