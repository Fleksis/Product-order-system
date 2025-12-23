<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        $validated = $request->validated();

        $user = User::create($validated);
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => [
                'status' => 'success',
                'message' => 'Register successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ],
            ],
        ]);
    }

    public function login(Request $request) {
        // Login method
    }

    public function logout(Request $request) {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'data' => [
                'status' => 'success',
                'message' => 'Logged out successfully',
            ],
        ]);
    }

    public function user() {
        return new UserResource(auth()->user());
    }
}
