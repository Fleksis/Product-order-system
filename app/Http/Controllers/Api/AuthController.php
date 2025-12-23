<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {
        $validated = $request->validated();

        $user = User::create($validated);

        return response()->json([
            'data' => [
                'status' => 'success',
                'message' => 'Register successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth-token')->plainTextToken,
                ],
            ],
        ]);
    }

    public function login(LoginRequest $request) {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'data' => [
                    'status' => 'error',
                    'message' => 'Invalid credentials',
                ],
            ], 401);
        }

        return response()->json([
            'data' => [
                'status' => 'success',
                'message' => 'Login successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth-token')->plainTextToken,
                ],
            ]
        ]);
    }

    public function logout() {
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
