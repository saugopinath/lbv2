<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'Invalid credentials',
                    'is_success' => false,
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not create token',
                'is_success' => false,
            ], 500);
        }
        return response()->json([
            'token' => $token,
            'is_success' => true,
        ], 200);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'message' => 'Logged out successfully',
                'is_logout' => true,
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Failed to logout',
                'is_logout' => false,
            ], 500);
        }
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json([
                    'error' => 'Token not provided',
                    'is_refresh' => false,
                ], 401);
            }
            $newToken = JWTAuth::refresh($token);
            return response()->json([
                'token' => $newToken,
                'is_refresh' => true,
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token refresh failed',
                'is_refresh' => false,
            ], 401);
        }
    }

    public function sendtolb(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json([
                    'is_sendtolb' => false,
                    'message' => 'Token not provided'
                ], 401);
            }
            $receivedData = $request->all();
            // dd($receivedData);
            return response()->json([
                'is_sendtolb' => $receivedData,
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'is_sendtolb' => false,
                'message' => 'Invalid or expired token',
            ], 401);
        }
    }
}
