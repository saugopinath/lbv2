<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'error' => 'Invalid credentials',
                'is_success' => false,
            ]);
        }
        return response()->json([
            'token' => $token,
            'is_success' => true,
        ]);
    }
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }
    public function refresh()
    {
        return response()->json([
            'token' => auth('api')->refresh()
        ]);
    }
    public function sendtolb()
    {
        return response()->json(['message' => 'You are authenticated to push data']);
    }
}
