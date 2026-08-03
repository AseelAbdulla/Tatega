<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * Display all users
     */
    public function index()
    {
        $users = $this->userService->getAllUsers();

        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }


    /**
     * Store user
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser(
            $request->validated()
        );

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }


    /**
     * Display user
     */
    public function show(string $id)
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {

            return response()->json([
                'message' => 'User not found'
            ], 404);

        }

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }


    /**
     * Update user
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = $this->userService->updateUser(
            $id,
            $request->validated()
        );

        if (!$user) {

            return response()->json([
                'message' => 'User not found'
            ], 404);

        }

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }


    /**
     * Delete user
     */
    public function destroy(string $id)
    {
        $deleted = $this->userService->deleteUser($id);

        if (!$deleted) {

            return response()->json([
                'message' => 'User not found'
            ], 404);

        }

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
