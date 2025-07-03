<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use function Laravel\Prompts\alert;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Regenerate the session to prevent session fixation attacks
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        }
        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    public function logout(Request $request)
    {

        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout successfully',
        ]);
    }
}
