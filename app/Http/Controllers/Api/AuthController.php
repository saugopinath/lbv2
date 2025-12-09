<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use App\Models\BackFromJb;

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
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return response()->json([
                'token'      => $newToken,
                'is_refresh' => true,
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'error'      => 'Token refresh failed',
                'is_refresh' => false,
            ], 401);
        }
    }

    public function sendtolb(Request $request)
    {
        $validated = $request->validate([
            'lb_application_id' => 'required|numeric',
            'jb_poposed_dob'    => 'required|date',
        ]);
        try {
            $backFromJb = new BackFromJb();
            $backFromJb->application_id = $validated['lb_application_id'];
            $backFromJb->jb_poposed_dob = $validated['jb_poposed_dob'];
            $backFromJb->next_level_role_id = 20;
            $backFromJb->save();
            return response()->json([
                'is_sendtolb' => true,
                'message' => 'Data saved successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'is_sendtolb' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
