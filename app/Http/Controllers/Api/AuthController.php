<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Log;
use Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return $this->errorResponse('Invalid credentials', Response::HTTP_UNAUTHORIZED);
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $token
            ], 'Login successful');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse(null, 'Logged out successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function signin(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // $user = User::where('email', $validated['email'])->first();
            $user = User::with('schedule', 'department', 'part')->where('email', $validated['email'])->first();


            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return $this->errorResponse('Invalid credentials', Response::HTTP_UNAUTHORIZED);
            }

            if ($user->status != 'active') {
                return $this->errorResponse('Your account is not active', Response::HTTP_UNAUTHORIZED);
            }

            // Generate API token
            $token = Str::random(80);
            $user->forceFill([
                'api_token' => $token
            ])->save();

            $userData = $user->toArray();

            return $this->successResponse([
                // 'user' => new UserResource($user),
                'user' => $userData,
                'token' => $token
            ], 'Login successful');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function me(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userData = $user->load('schedule', 'department', 'part');

            return $this->successResponse(
                new UserResource($userData),
                'User retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function signout(Request $request)
    {
        try {
            $request->user()->update(['api_token' => null]);
            return $this->successResponse(null, 'Logged out successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            $token = Str::random(80);
            $request->user()->forceFill([
                'api_token' => $token
            ])->save();

            return $this->successResponse([
                'token' => $token
            ], 'Token refreshed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}