<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Logic cho dashboard admin
        return response()->json(['message' => 'Welcome to the admin dashboard']);
    }

    
}
