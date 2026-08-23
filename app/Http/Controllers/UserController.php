<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * =========================================================
     * Display all users
     * =========================================================
     */
    public function index()
    {
        $users = $this->userService->getAllUsers();

        return response()->json([
            'status' => true,

            'message' => 'تم جلب المستخدمين بنجاح.',

            'data' => $users,
        ], 200);
    }

    /**
 * =========================================================
 * Display local customers
 * =========================================================
 */
public function localCustomers()
{
    $customers = $this->userService->getLocalCustomers();

    return response()->json([
        'status' => true,
        'message' => 'تم جلب العملاء المحليين بنجاح.',
        'data' => $customers,
    ], 200);
}

    /**
     * =========================================================
     * Store user
     * =========================================================
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser(
            $request->validated()
        );

        return response()->json([
            'status' => true,

            'message' => 'تم إنشاء المستخدم بنجاح.',

            'data' => $user,
        ], 201);
    }

    /**
     * =========================================================
     * Display one user
     * =========================================================
     */
    public function show(string $id)
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json([
                'status' => false,

                'message' => 'المستخدم غير موجود.',
            ], 404);
        }

        return response()->json([
            'status' => true,

            'message' => 'تم جلب بيانات المستخدم بنجاح.',

            'data' => $user,
        ], 200);
    }

    /**
     * =========================================================
     * Update user
     * =========================================================
     */
    public function update(
        UpdateUserRequest $request,
        string $id
    ) {
        $user = $this->userService->updateUser(
            $id,
            $request->validated()
        );

        if (!$user) {
            return response()->json([
                'status' => false,

                'message' => 'المستخدم غير موجود.',
            ], 404);
        }

        return response()->json([
            'status' => true,

            'message' => 'تم تحديث المستخدم بنجاح.',

            'data' => $user,
        ], 200);
    }

    /**
     * =========================================================
     * Delete user
     * =========================================================
     */
    public function destroy(string $id)
    {
        $deleted = $this->userService->deleteUser($id);

        if (!$deleted) {
            return response()->json([
                'status' => false,

                'message' => 'المستخدم غير موجود.',
            ], 404);
        }

        return response()->json([
            'status' => true,

            'message' => 'تم حذف المستخدم بنجاح.',
        ], 200);
    }
}
