<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
 * تغيير كلمة المرور للمستخدم المسجل دخوله.
 */
public function changePassword(Request $request)
{
    $validated = $request->validate([
        'current_password' => ['required', 'current_password'],
        'new_password'     => ['required', Password::defaults(), 'confirmed'],
    ]);

    $request->user()->update([
        'password' => Hash::make($validated['new_password']),
    ]);

    return response()->json([
        'status'  => 'success',
        'message' => 'تم تغيير كلمة المرور بنجاح.',
    ]);
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
    public function profile(Request $request)
    {
        // جلب المستخدم الحالي مع العنوان المرتبط به
        $user = $request->user()->load('address');

        return response()->json([
            'status' => true,
            'data' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'phone'    => $user->phone,
                'roles'    => $user->getRoleNames(),
                // إرسال تفاصيل العنوان كما هي مخزنة في الجدول المنفصل
                'address'  => $user->address ? [
                    'country'  => $user->address->country,
                    'city'     => $user->address->city,
                    'region'   => $user->address->region,
                    'street'   => $user->address->street,
                    'building' => $user->address->building,
                    'notes'    => $user->address->notes,
                ] : null
            ]
        ]);
    }

    /**
     * تحديث بيانات المستخدم الشخصية وتفاصيل العنوان.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // 1. التحقق من صحة البيانات القادمة
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'country'  => 'required|string|max:100',
            'city'     => 'required|string|max:100',
            'region'   => 'required|string|max:100',
            'street'   => 'required|string|max:255',
            'building' => 'required|string|max:100',
            'notes'    => 'nullable|string',
        ]);

        // 2. تحديث جدول المستخدم وجدول العنوان داخل Transaction
        DB::transaction(function () use ($user, $validated) {
            // تحديث بيانات المستخدم الأساسية
            $user->update([
                'name'  => $validated['name'],
                'phone' => $validated['phone'],
            ]);

            // تحديث أو إنشاء كائن العنوان المرتبط بالمستخدم
            $user->address()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'country'  => $validated['country'],
                    'city'     => $validated['city'],
                    'region'   => $validated['region'],
                    'street'   => $validated['street'],
                    'building' => $validated['building'],
                    'notes'    => $validated['notes'] ?? null,
                ]
            );
        });

        // 3. إعادة إرجاع بيانات المستخدم المعالجة مع العلاقة (address)
        $user->load('address');

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تحديث الملف الشخصي والعنوان بنجاح.',
            'data'    => $user,
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
