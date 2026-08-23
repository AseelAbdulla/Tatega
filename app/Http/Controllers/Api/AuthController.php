<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\InternationalImportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * ============================================================
     * REGISTER
     * ============================================================
     *
     * POST /api/register
     *
     * التسجيل العام للمستخدمين.
     *
     * المستخدم يختار:
     *
     * local
     * international
     *
     * Laravel هو المسؤول عن تحديد الـRole.
     *
     * local
     *      ↓
     * local-client
     *
     * international
     *      ↓
     * international-pending
     *
     * ولا يتم إعطاء:
     *
     * international-client
     *
     * إلا بعد موافقة الإدارة.
     */
    public function register(Request $request): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'customer_type' => [
                'required',
                'string',
                'in:local,international',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Create User
        |--------------------------------------------------------------------------
        |
        | customer_type يتم حفظه في users.
        |
        */

        $user = User::create([
            'name' => $validated['name'],

            'email' => $validated['email'],

            'phone' => $validated['phone'],

            'customer_type' => $validated['customer_type'],

            'password' => $validated['password'],

            'status' => 'active',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Determine Initial Role
        |--------------------------------------------------------------------------
        |
        | local
        |      ↓
        | local-client
        |
        | international
        |      ↓
        | international-pending
        |
        | لا نعطي international-client عند التسجيل.
        |
        */

        if ($validated['customer_type'] === 'local') {

            $roleName = 'local-client';

        } else {

            $roleName = 'international-pending';

        }


        /*
        |--------------------------------------------------------------------------
        | Make Sure Role Exists
        |--------------------------------------------------------------------------
        */

        Role::firstOrCreate(
            [
                'name' => $roleName,
                'guard_name' => 'sanctum',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Assign Initial Role
        |--------------------------------------------------------------------------
        */

        $user->assignRole($roleName);


        /*
        |--------------------------------------------------------------------------
        | Get Role
        |--------------------------------------------------------------------------
        */

        $role = $user
            ->getRoleNames()
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Get Roles
        |--------------------------------------------------------------------------
        */

        $roles = $user
            ->getRoleNames()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Get Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = $user
            ->getAllPermissions()
            ->pluck('name')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | International Request
        |--------------------------------------------------------------------------
        |
        | لا ننشئ طلب استيراد عند التسجيل.
        |
        | العميل الدولي سيقوم بتقديم الوثيقة بعد تسجيل الدخول.
        |
        */

        $internationalRequest = null;

        $internationalRequestStatus = null;


        /*
        |--------------------------------------------------------------------------
        | Registration State
        |--------------------------------------------------------------------------
        */

        if ($validated['customer_type'] === 'local') {

            $registrationState = 'local_active';

        } else {

            $registrationState = 'international_pending_document';

        }


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        |
        | المحلي:
        |      يدخل Dashboard المحلي بعد Login.
        |
        | الدولي:
        |      لا يدخل Dashboard الدولي عند التسجيل.
        |
        */

        if ($role === 'local-client') {

            $dashboard = '/customer/dashboard';

        } else {

            $dashboard = null;

        }


        /*
        |--------------------------------------------------------------------------
        | Next Step
        |--------------------------------------------------------------------------
        */

        if ($validated['customer_type'] === 'local') {

            $nextStep = 'login';

            $nextRoute = '/login';

        } else {

            $nextStep = 'login_then_submit_international_document';

            /*
            | الصفحة التي سنجعل Login يوجه إليها لاحقًا.
            |
            | لا تعتبر Dashboard.
            |
            */

            $nextRoute = '/customer/international-import';

        }


        /*
        |--------------------------------------------------------------------------
        | Create Token
        |--------------------------------------------------------------------------
        |
        | نبقي إنشاء Token كما كان موجودًا في النظام.
        |
        | React لن يستخدمه مباشرة بعد التسجيل،
        | لأن السيناريو ينقل المستخدم إلى Login.
        |
        */

        $token = $user
            ->createToken('auth-token')
            ->plainTextToken;


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'message' => 'تم إنشاء الحساب بنجاح',

            'data' => [

                'user' => [

                    'id' => $user->id,

                    'name' => $user->name,

                    'email' => $user->email,

                    'phone' => $user->phone,

                    'customer_type' => $user->customer_type,

                    'status' => $user->status,

                    'role' => $role,

                    'roles' => $roles,

                    'permissions' => $permissions,

                    'dashboard' => $dashboard,

                    'registration_state' => $registrationState,

                    'next_step' => $nextStep,

                    'next_route' => $nextRoute,

                    'international_request_status' =>
                        $internationalRequestStatus,

                    'international_request' =>
                        $internationalRequest,

                ],

                'token' => $token,

                'token_type' => 'Bearer',

            ],

        ], 201);
    }


    /**
     * ============================================================
     * LOGIN
     * ============================================================
     *
     * POST /api/login
     *
     * Laravel يحدد المسار الحقيقي للمستخدم.
     */
    public function login(Request $request): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Attempt Login
        |--------------------------------------------------------------------------
        */

        if (
            !Auth::attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ])
        ) {

            throw ValidationException::withMessages([
                'email' => [
                    'البريد الإلكتروني أو كلمة المرور غير صحيحة',
                ],
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Authenticated User
        |--------------------------------------------------------------------------
        */

        /** @var \App\Models\User $user */

        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | Account Status
        |--------------------------------------------------------------------------
        */

        if (
            isset($user->status)
            &&
            $user->status !== 'active'
        ) {

            Auth::logout();

            return response()->json([

                'success' => false,

                'message' => 'هذا الحساب غير مفعل',

            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | Customer Type
        |--------------------------------------------------------------------------
        */

        $customerType = $user->customer_type;


        /*
        |--------------------------------------------------------------------------
        | Get Real Role
        |--------------------------------------------------------------------------
        */

        $role = $user
            ->getRoleNames()
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Get Roles
        |--------------------------------------------------------------------------
        */

        $roles = $user
            ->getRoleNames()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Get Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = $user
            ->getAllPermissions()
            ->pluck('name')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | INTERNATIONAL REQUEST
        |--------------------------------------------------------------------------
        |
        | نبحث عن طلب الاستيراد للعميل الدولي فقط.
        |
        */

        $internationalRequest = null;

        $internationalRequestStatus = null;


        if (
            $customerType === 'international'
        ) {

            $internationalRequest =
                InternationalImportRequest::query()
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->latest()
                    ->first();


            if ($internationalRequest) {

                $internationalRequestStatus =
                    $internationalRequest->status;

            }
        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN STATE
        |--------------------------------------------------------------------------
        */

        $loginState = 'active';


        /*
        |--------------------------------------------------------------------------
        | NEXT STEP
        |--------------------------------------------------------------------------
        */

        $nextStep = 'dashboard';

        $nextRoute = null;


        /*
        |--------------------------------------------------------------------------
        | LOCAL CLIENT
        |--------------------------------------------------------------------------
        |
        | العميل المحلي يدخل Dashboard مباشرة.
        |
        */

        if (
            $customerType === 'local'
            &&
            $role === 'local-client'
        ) {

            $loginState = 'local_active';

            $nextStep = 'local_dashboard';

            $nextRoute = '/customer/dashboard';
        }


        /*
        |--------------------------------------------------------------------------
        | INTERNATIONAL - PENDING ROLE
        |--------------------------------------------------------------------------
        |
        | العميل الدولي الذي لم يقدم الوثيقة بعد.
        |
        */

        if (
            $customerType === 'international'
            &&
            $role === 'international-pending'
            &&
            !$internationalRequest
        ) {

            $loginState =
                'international_pending_document';

            $nextStep =
                'submit_international_document';

            $nextRoute =
                '/customer/international-import';
        }


        /*
        |--------------------------------------------------------------------------
        | INTERNATIONAL - REQUEST PENDING
        |--------------------------------------------------------------------------
        |
        | تم إرسال الوثيقة.
        |
        | لا يدخل Dashboard الدولي.
        |
        */

        if (
            $customerType === 'international'
            &&
            $internationalRequest
            &&
            $internationalRequestStatus === 'pending'
        ) {

            $loginState =
                'international_request_pending';

            $nextStep =
                'wait_for_admin_approval';

            $nextRoute =
                '/customer/international-import';
        }


        /*
        |--------------------------------------------------------------------------
        | INTERNATIONAL - REQUEST REJECTED
        |--------------------------------------------------------------------------
        |
        | الإدارة رفضت الطلب.
        |
        | يبقى المستخدم غير معتمد.
        |
        */

        if (
            $customerType === 'international'
            &&
            $internationalRequest
            &&
            $internationalRequestStatus === 'rejected'
        ) {

            $loginState =
                'international_rejected';

            $nextStep =
                'international_request_rejected';

            $nextRoute =
                '/customer/international-import';
        }


        /*
        |--------------------------------------------------------------------------
        | INTERNATIONAL - APPROVED
        |--------------------------------------------------------------------------
        |
        | في حالة الموافقة:
        |
        | Role يجب أن يكون:
        |
        | international-client
        |
        | وهنا فقط يسمح له بدخول Dashboard الدولي.
        |
        */

        if (
            $customerType === 'international'
            &&
            $role === 'international-client'
        ) {

            $loginState =
                'international_active';

            $nextStep =
                'international_dashboard';

            $nextRoute =
                '/customer/dashboard';
        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if (
            $role === 'admin'
        ) {

            $loginState =
                'admin_active';

            $nextStep =
                'admin_dashboard';

            $nextRoute =
                '/admin/dashboard';
        }


        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE
        |--------------------------------------------------------------------------
        */

        if (
            $role === 'employee'
        ) {

            $loginState =
                'employee_active';

            $nextStep =
                'admin_dashboard';

            $nextRoute =
                '/admin/dashboard';
        }


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        |
        | مهم:
        |
        | international-pending
        | rejected
        |
        | لا تحصل على Dashboard الدولي.
        |
        */

        $dashboard = null;


        if (
            $role === 'local-client'
            &&
            $customerType === 'local'
        ) {

            $dashboard =
                '/customer/dashboard';
        }


        if (
            $role === 'international-client'
            &&
            $customerType === 'international'
        ) {

            $dashboard =
                '/customer/dashboard';
        }


        if (
            $role === 'admin'
            ||
            $role === 'employee'
        ) {

            $dashboard =
                '/admin/dashboard';
        }


        /*
        |--------------------------------------------------------------------------
        | Delete Old Tokens
        |--------------------------------------------------------------------------
        */

        $user->tokens()->delete();


        /*
        |--------------------------------------------------------------------------
        | Create New Token
        |--------------------------------------------------------------------------
        */

        $token = $user
            ->createToken('auth-token')
            ->plainTextToken;


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'message' => 'تم تسجيل الدخول بنجاح',

            'data' => [

                'user' => [

                    'id' => $user->id,

                    'name' => $user->name,

                    'email' => $user->email,

                    'phone' => $user->phone,

                    /*
                    |--------------------------------------------------------------------------
                    | Customer Type
                    |--------------------------------------------------------------------------
                    */

                    'customer_type' =>
                        $customerType,

                    'status' =>
                        $user->status,

                    /*
                    |--------------------------------------------------------------------------
                    | Role
                    |--------------------------------------------------------------------------
                    */

                    'role' =>
                        $role,

                    'roles' =>
                        $roles,

                    /*
                    |--------------------------------------------------------------------------
                    | Permissions
                    |--------------------------------------------------------------------------
                    */

                    'permissions' =>
                        $permissions,

                    /*
                    |--------------------------------------------------------------------------
                    | Dashboard
                    |--------------------------------------------------------------------------
                    */

                    'dashboard' =>
                        $dashboard,

                    /*
                    |--------------------------------------------------------------------------
                    | Login State
                    |--------------------------------------------------------------------------
                    */

                    'login_state' =>
                        $loginState,

                    /*
                    |--------------------------------------------------------------------------
                    | Next Step
                    |--------------------------------------------------------------------------
                    */

                    'next_step' =>
                        $nextStep,

                    'next_route' =>
                        $nextRoute,

                    /*
                    |--------------------------------------------------------------------------
                    | International Request
                    |--------------------------------------------------------------------------
                    */

                    'international_request_status' =>
                        $internationalRequestStatus,

                    'international_request' =>
                        $internationalRequest,

                ],

                'token' =>
                    $token,

                'token_type' =>
                    'Bearer',

            ],

        ], 200);
    }


    /**
     * ============================================================
     * CURRENT USER
     * ============================================================
     *
     * GET /api/me
     */
    public function me(Request $request): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Authenticated User
        |--------------------------------------------------------------------------
        */

        /** @var \App\Models\User $user */

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Customer Type
        |--------------------------------------------------------------------------
        */

        $customerType =
            $user->customer_type;


        /*
        |--------------------------------------------------------------------------
        | Role
        |--------------------------------------------------------------------------
        */

        $role = $user
            ->getRoleNames()
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $roles = $user
            ->getRoleNames()
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = $user
            ->getAllPermissions()
            ->pluck('name')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | International Request
        |--------------------------------------------------------------------------
        */

        $internationalRequest = null;

        $internationalRequestStatus = null;


        if (
            $customerType === 'international'
        ) {

            $internationalRequest =
                InternationalImportRequest::query()
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->latest()
                    ->first();


            if ($internationalRequest) {

                $internationalRequestStatus =
                    $internationalRequest->status;

            }
        }


        /*
        |--------------------------------------------------------------------------
        | Login State
        |--------------------------------------------------------------------------
        */

        $loginState = 'active';

        $nextStep = 'dashboard';

        $nextRoute = null;


        /*
        |--------------------------------------------------------------------------
        | LOCAL
        |--------------------------------------------------------------------------
        */

        if (
            $customerType === 'local'
            &&
            $role === 'local-client'
        ) {

            $loginState =
                'local_active';

            $nextStep =
                'local_dashboard';

            $nextRoute =
                '/customer/dashboard';
        }


        /*
        |--------------------------------------------------------------------------
        | INTERNATIONAL - NO REQUEST
        |--------------------------------------------------------------------------
        */

        if (
            $customerType === 'international'
            &&
            $role === 'international-pending'
            &&
            !$internationalRequest
        ) {

            $loginState =
                'international_pending_document';

            $nextStep =
                'submit_international_document';

            $nextRoute =
                '/customer/international-import';
        }


        /*
        |--------------------------------------------------------------------------
        | INTERNATIONAL - PENDING
        |--------------------------------------------------------------------------
        */

        if (
            $customerType === 'international'
            &&
            $internationalRequest
            &&
            $internationalRequestStatus === 'pending'
        ) {

            $loginState =
                'international_request_pending';

            $nextStep =
                'wait_for_admin_approval';

            $nextRoute =
                '/customer/international-import';
        }


        /*
        |--------------------------------------------------------------------------
        | INTERNATIONAL - REJECTED
        |--------------------------------------------------------------------------
        */

        if (
            $customerType === 'international'
            &&
            $internationalRequest
            &&
            $internationalRequestStatus === 'rejected'
        ) {

            $loginState =
                'international_rejected';

            $nextStep =
                'international_request_rejected';

            $nextRoute =
                '/customer/international-import';
        }


        /*
        |--------------------------------------------------------------------------
        | INTERNATIONAL - APPROVED
        |--------------------------------------------------------------------------
        */

        if (
            $customerType === 'international'
            &&
            $role === 'international-client'
        ) {

            $loginState =
                'international_active';

            $nextStep =
                'international_dashboard';

            $nextRoute =
                '/customer/dashboard';
        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if (
            $role === 'admin'
        ) {

            $loginState =
                'admin_active';

            $nextStep =
                'admin_dashboard';

            $nextRoute =
                '/admin/dashboard';
        }


        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE
        |--------------------------------------------------------------------------
        */

        if (
            $role === 'employee'
        ) {

            $loginState =
                'employee_active';

            $nextStep =
                'admin_dashboard';

            $nextRoute =
                '/admin/dashboard';
        }


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        $dashboard = null;


        if (
            $customerType === 'local'
            &&
            $role === 'local-client'
        ) {

            $dashboard =
                '/customer/dashboard';
        }


        if (
            $customerType === 'international'
            &&
            $role === 'international-client'
        ) {

            $dashboard =
                '/customer/dashboard';
        }


        if (
            $role === 'admin'
            ||
            $role === 'employee'
        ) {

            $dashboard =
                '/admin/dashboard';
        }


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'message' =>
                'تم جلب بيانات المستخدم',

            'data' => [

                'user' => [

                    'id' =>
                        $user->id,

                    'name' =>
                        $user->name,

                    'email' =>
                        $user->email,

                    'phone' =>
                        $user->phone,

                    /*
                    |--------------------------------------------------------------------------
                    | Customer Type
                    |--------------------------------------------------------------------------
                    */

                    'customer_type' =>
                        $customerType,

                    'status' =>
                        $user->status,

                    /*
                    |--------------------------------------------------------------------------
                    | Role
                    |--------------------------------------------------------------------------
                    */

                    'role' =>
                        $role,

                    'roles' =>
                        $roles,

                    /*
                    |--------------------------------------------------------------------------
                    | Permissions
                    |--------------------------------------------------------------------------
                    */

                    'permissions' =>
                        $permissions,

                    /*
                    |--------------------------------------------------------------------------
                    | Dashboard
                    |--------------------------------------------------------------------------
                    */

                    'dashboard' =>
                        $dashboard,

                    /*
                    |--------------------------------------------------------------------------
                    | Login State
                    |--------------------------------------------------------------------------
                    */

                    'login_state' =>
                        $loginState,

                    /*
                    |--------------------------------------------------------------------------
                    | Next Step
                    |--------------------------------------------------------------------------
                    */

                    'next_step' =>
                        $nextStep,

                    'next_route' =>
                        $nextRoute,

                    /*
                    |--------------------------------------------------------------------------
                    | International Request
                    |--------------------------------------------------------------------------
                    */

                    'international_request_status' =>
                        $internationalRequestStatus,

                    'international_request' =>
                        $internationalRequest,

                ],

                /*
                |--------------------------------------------------------------------------
                | Compatibility
                |--------------------------------------------------------------------------
                */

                'customer_type' =>
                    $customerType,

                'role' =>
                    $role,

                'roles' =>
                    $roles,

                'permissions' =>
                    $permissions,

                'dashboard' =>
                    $dashboard,

                'login_state' =>
                    $loginState,

                'next_step' =>
                    $nextStep,

                'next_route' =>
                    $nextRoute,

                'international_request_status' =>
                    $internationalRequestStatus,

                'international_request' =>
                    $internationalRequest,

            ],

        ], 200);
    }


    /**
     * ============================================================
     * CHANGE PASSWORD
     * ============================================================
     *
     * PATCH /api/customer/password
     */
    public function changePassword(
        Request $request
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'current_password' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Authenticated User
        |--------------------------------------------------------------------------
        */

        /** @var \App\Models\User $user */

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Check Current Password
        |--------------------------------------------------------------------------
        */

        if (
            !Hash::check(
                $validated['current_password'],
                $user->password
            )
        ) {

            return response()->json([

                'success' => false,

                'message' =>
                    'كلمة المرور الحالية غير صحيحة.',

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Update Password
        |--------------------------------------------------------------------------
        */

        $user->password =
            $validated['password'];

        $user->save();


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'message' =>
                'تم تغيير كلمة المرور بنجاح.',

        ], 200);
    }


    /**
     * ============================================================
     * LOGOUT
     * ============================================================
     *
     * POST /api/logout
     */
    public function logout(
        Request $request
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Current Token
        |--------------------------------------------------------------------------
        */

        $token = $request
            ->user()
            ->currentAccessToken();


        /*
        |--------------------------------------------------------------------------
        | Delete Token
        |--------------------------------------------------------------------------
        */

        if ($token) {

            $token->delete();
        }


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'message' =>
                'تم تسجيل الخروج بنجاح',

        ], 200);
    }
}
