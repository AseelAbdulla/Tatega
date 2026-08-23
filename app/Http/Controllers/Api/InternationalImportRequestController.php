<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\InternationalImportRequest;
use App\Models\InternalNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InternationalImportRequestController extends Controller
{
    /**
     * ============================================================
     * CREATE INTERNATIONAL IMPORT REQUEST
     * ============================================================
     *
     * POST /api/customer/international-import
     *
     * العميل الدولي يرفع وثيقة الاعتماد.
     */
    public function store(Request $request): JsonResponse
    {
        /**
         * --------------------------------------------------------
         * Authenticated User
         * --------------------------------------------------------
         */

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً.',
            ], 401);
        }

        /**
         * --------------------------------------------------------
         * التأكد أن المستخدم عميل دولي
         * --------------------------------------------------------
         */

        if ($user->customer_type !== 'international') {
            return response()->json([
                'success' => false,
                'message' => 'هذا الطلب مخصص للعملاء الدوليين فقط.',
            ], 403);
        }

        /**
         * --------------------------------------------------------
         * منع إرسال طلب جديد إذا كان هناك طلب Pending
         * --------------------------------------------------------
         */

        $existingPendingRequest = InternationalImportRequest::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($existingPendingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'لديك طلب استيراد دولي قيد المراجعة بالفعل.',
                'data' => [
                    'request' => $existingPendingRequest,
                    'status' => $existingPendingRequest->status,
                ],
            ], 422);
        }

        /**
         * --------------------------------------------------------
         * Validation
         * --------------------------------------------------------
         */

        $validated = $request->validate([
            'document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'country' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        /**
         * --------------------------------------------------------
         * Upload Document
         * --------------------------------------------------------
         */

        $documentPath = $request
            ->file('document')
            ->store(
                'international-documents',
                'public'
            );

        /**
         * --------------------------------------------------------
         * Generate Request Number
         * --------------------------------------------------------
         */

        $requestNumber = 'IMP-' .
            now()->format('YmdHis') .
            '-' .
            $user->id;

        /**
         * --------------------------------------------------------
         * Create International Import Request
         * --------------------------------------------------------
         */

        $internationalRequest = InternationalImportRequest::create([
            'user_id' => $user->id,

            'request_number' => $requestNumber,

            'title' => $validated['title'],

            'country' => $validated['country'],

            'description' =>
                $validated['description'] ?? null,

            'document_path' => $documentPath,

            'status' => 'pending',
        ]);

        /**
         * ========================================================
         * CREATE ADMIN / EMPLOYEE NOTIFICATIONS
         * ========================================================
         *
         * العميل
         *     ↓
         * رفع الوثيقة
         *     ↓
         * InternationalImportRequest
         *     ↓
         * Pending
         *     ↓
         * Notification
         *     ↓
         * Admin / Employee
         */

        $adminUsers = User::role([
            'admin',
            'employee',
            'manager',
        ])->get();

        /**
         * --------------------------------------------------------
         * إنشاء إشعار لكل مستخدم إداري مخول
         * --------------------------------------------------------
         */

        foreach ($adminUsers as $adminUser) {
            InternalNotification::create([
                'user_id' => $adminUser->id,

                'title' => [
                    'ar' => 'طلب اعتماد مستورد دولي جديد',

                    'en' => 'New International Import Request',
                ],

                'message' => [
                    'ar' =>
                        'قام العميل ' .
                        ($user->name ?? 'عميل دولي') .
                        ' بإرسال طلب اعتماد مستورد دولي رقم ' .
                        $internationalRequest->request_number .
                        ' ويحتاج إلى المراجعة.',

                    'en' =>
                        'Customer ' .
                        ($user->name ?? 'International Customer') .
                        ' submitted international importer request ' .
                        $internationalRequest->request_number .
                        ' and it requires review.',
                ],

                'type' =>
                    'international_import_pending',

                'is_read' => false,

                'sent_at' => now(),
            ]);
        }

        /**
         * --------------------------------------------------------
         * Response
         * --------------------------------------------------------
         */

        return response()->json([
            'success' => true,

            'message' =>
                'تم إرسال وثيقة الاعتماد بنجاح، والطلب الآن قيد المراجعة.',

            'data' => [
                'request' => $internationalRequest,

                'status' =>
                    $internationalRequest->status,

                'notification_sent' =>
                    $adminUsers->count() > 0,
            ],
        ], 201);
    }


    /**
     * ============================================================
     * GET CURRENT USER INTERNATIONAL IMPORT REQUEST
     * ============================================================
     *
     * GET /api/customer/international-import
     *
     * يعيد آخر طلب للعميل الحالي.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً.',
            ], 401);
        }

        $internationalRequest =
            InternationalImportRequest::query()
                ->where('user_id', $user->id)
                ->latest()
                ->first();

        if (!$internationalRequest) {
            return response()->json([
                'success' => true,

                'message' =>
                    'لا يوجد طلب استيراد دولي حتى الآن.',

                'data' => [
                    'request' => null,
                    'status' => null,
                ],
            ]);
        }

        return response()->json([
            'success' => true,

            'message' =>
                'تم جلب طلب الاستيراد الدولي.',

            'data' => [
                'request' =>
                    $internationalRequest,

                'status' =>
                    $internationalRequest->status,
            ],
        ]);
    }


    /**
     * ============================================================
     * SHOW INTERNATIONAL IMPORT REQUEST
     * ============================================================
     *
     * GET /api/customer/international-import/{id}
     */
    public function show(
        Request $request,
        InternationalImportRequest $internationalImportRequest
    ): JsonResponse {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً.',
            ], 401);
        }

        /**
         * --------------------------------------------------------
         * التأكد أن الطلب يخص المستخدم الحالي
         * --------------------------------------------------------
         */

        if ($internationalImportRequest->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بعرض هذا الطلب.',
            ], 403);
        }

        return response()->json([
            'success' => true,

            'message' =>
                'تم جلب طلب الاستيراد الدولي.',

            'data' => [
                'request' =>
                    $internationalImportRequest,

                'status' =>
                    $internationalImportRequest->status,
            ],
        ]);
    }


    /**
     * ============================================================
     * UPDATE INTERNATIONAL IMPORT REQUEST
     * ============================================================
     *
     * PUT /api/customer/international-import/{id}
     *
     * يستخدم عند السماح للعميل بتعديل بيانات الطلب.
     */
    public function update(
        Request $request,
        InternationalImportRequest $internationalImportRequest
    ): JsonResponse {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً.',
            ], 401);
        }

        /**
         * --------------------------------------------------------
         * التأكد أن الطلب يخص المستخدم
         * --------------------------------------------------------
         */

        if ($internationalImportRequest->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذا الطلب.',
            ], 403);
        }

        /**
         * --------------------------------------------------------
         * التعديل مسموح فقط إذا كان الطلب مرفوضًا
         * --------------------------------------------------------
         */

        if ($internationalImportRequest->status !== 'rejected') {
            return response()->json([
                'success' => false,
                'message' =>
                    'لا يمكن تعديل الطلب إلا بعد رفضه.',
            ], 422);
        }

        /**
         * --------------------------------------------------------
         * Validation
         * --------------------------------------------------------
         */

        $validated = $request->validate([
            'document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'country' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        /**
         * --------------------------------------------------------
         * Update Document if uploaded
         * --------------------------------------------------------
         */

        if ($request->hasFile('document')) {

            $documentPath = $request
                ->file('document')
                ->store(
                    'international-documents',
                    'public'
                );

            $internationalImportRequest->document_path =
                $documentPath;
        }

        /**
         * --------------------------------------------------------
         * Update Data
         * --------------------------------------------------------
         */

        $internationalImportRequest->title =
            $validated['title'];

        $internationalImportRequest->country =
            $validated['country'];

        $internationalImportRequest->description =
            $validated['description'] ?? null;

        /**
         * --------------------------------------------------------
         * Return request to Pending
         * --------------------------------------------------------
         */

        $internationalImportRequest->status = 'pending';

        /**
         * إذا كان النظام يحتوي على سبب الرفض
         * ونريد مسحه بعد إعادة التقديم،
         * يمكن إضافته هنا لاحقًا.
         */

        $internationalImportRequest->save();

        /**
         * --------------------------------------------------------
         * Create new admin notification
         * --------------------------------------------------------
         */

        $adminUsers = User::role([
            'admin',
            'employee',
            'manager',
        ])->get();

        foreach ($adminUsers as $adminUser) {
            InternalNotification::create([
                'user_id' => $adminUser->id,

                'title' => [
                    'ar' => 'إعادة تقديم طلب مستورد دولي',

                    'en' => 'International Import Request Resubmitted',
                ],

                'message' => [
                    'ar' =>
                        'قام العميل ' .
                        ($user->name ?? 'عميل دولي') .
                        ' بإعادة تقديم طلب الاستيراد الدولي رقم ' .
                        $internationalImportRequest->request_number .
                        ' ويحتاج إلى المراجعة.',

                    'en' =>
                        'Customer ' .
                        ($user->name ?? 'International Customer') .
                        ' resubmitted international import request ' .
                        $internationalImportRequest->request_number .
                        ' and it requires review.',
                ],

                'type' =>
                    'international_import_pending',

                'is_read' => false,

                'sent_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,

            'message' =>
                'تم تعديل الطلب وإعادة إرساله للمراجعة.',

            'data' => [
                'request' =>
                    $internationalImportRequest,

                'status' =>
                    $internationalImportRequest->status,
            ],
        ]);
    }


    /**
     * ============================================================
     * DELETE INTERNATIONAL IMPORT REQUEST
     * ============================================================
     *
     * DELETE /api/customer/international-import/{id}
     */
    public function destroy(
        Request $request,
        InternationalImportRequest $internationalImportRequest
    ): JsonResponse {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً.',
            ], 401);
        }

        /**
         * --------------------------------------------------------
         * التأكد أن الطلب يخص المستخدم
         * --------------------------------------------------------
         */

        if ($internationalImportRequest->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذا الطلب.',
            ], 403);
        }

        /**
         * --------------------------------------------------------
         * لا نسمح بالحذف بعد الموافقة
         * --------------------------------------------------------
         */

        if ($internationalImportRequest->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' =>
                    'لا يمكن حذف طلب تمت الموافقة عليه.',
            ], 422);
        }

        $internationalImportRequest->delete();

        return response()->json([
            'success' => true,

            'message' =>
                'تم حذف طلب الاستيراد الدولي بنجاح.',
        ]);
    }
}
