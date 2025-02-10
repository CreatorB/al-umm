<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends ApiController
{
    public function index()
    {
        try {
            $users = User::paginate(10);
            return $this->successResponse(
                UserResource::collection($users),
                'Users retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show(User $user)
    {
        try {
            return $this->successResponse(
                new UserResource($user),
                'User retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8',
            ]);

            $user = User::create($validated);

            return $this->successResponse(
                new UserResource($user),
                'User created successfully',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}