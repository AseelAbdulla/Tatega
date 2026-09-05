<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewImportRequest;
use App\Http\Requests\StoreImportRequest;
use App\Http\Resources\ImportRequestResource;
use App\Models\ImportRequest;
use App\Models\InternalNotification; // 👈 1. استدعاء الموديل
use App\Services\ImportRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImportRequestController extends Controller
{
    public function __construct(
        protected ImportRequestService $importRequestService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        // إذا كان المستخدم أدمن، نرجع كل الطلبات للوحة التحكم
        if ($user->hasRole('admin') || $user->can('view-orders')) {
            $requests = ImportRequest::with(['user', 'items.product', 'items.unit', 'items.product.images'])
                ->latest()
                ->get();
        } else {
            // إذا كان عميل عادي، نرجع طلباته هو فقط
            $requests = $user->importRequests()
                ->with(['items.product', 'items.unit', 'items.product.images'])
                ->latest()
                ->get();
        }

        return ImportRequestResource::collection($requests);
    }

    // تقديم طلب استيراد جديد من العميل
    public function store(StoreImportRequest $request): JsonResponse
    {
        $importRequest = $this->importRequestService->createRequest(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'status'  => true,
            'message' => 'تم تقديم طلب الاستيراد بنجاح وهو قيد المراجعة.',
            'data'    => new ImportRequestResource($importRequest),
        ], 201);
    }

    // عرض تفاصيل طلب محدد
    public function show(ImportRequest $importRequest): ImportRequestResource
    {
        $importRequest->load(['items.product', 'items.unit', 'user']);
        return new ImportRequestResource($importRequest);
    }

    // مراجعة الطلب من قبل المدير (قبول وإرسال عرض سعر / رفض)
    public function review(ReviewImportRequest $request, ImportRequest $importRequest): JsonResponse
    {
        $updatedRequest = $this->importRequestService->reviewRequest(
            $importRequest,
            $request->validated()
        );

        // 🔔 2. إنشاء الإشعار وإدراجه في جدول internal_notifications
        $isRejected = $request->action === 'reject';

        InternalNotification::create([
            'user_id' => $importRequest->user_id, // العميل صاحب الطلب
            'title' => [
                'ar' => $isRejected ? 'تم رفض طلب الاستيراد' : 'تم تسعير طلب الاستيراد',
                'en' => $isRejected ? 'Import Request Rejected' : 'Import Request Priced'
            ],
            'message' => [
                'ar' => $isRejected
                    ? "عذراً، تم رفض طلب الاستيراد الخاص بك رقم (#{$importRequest->id})."
                    : "تمت مراجعة وتسعير طلب الاستيراد رقم (#{$importRequest->id}) وإرسال عرض السعر.",
                'en' => $isRejected
                    ? "Your import request (#{$importRequest->id}) has been rejected."
                    : "Your import request (#{$importRequest->id}) has been priced."
            ],
            'type' => $isRejected ? 'import_request_rejected' : 'import_request_reviewed',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => $isRejected ? 'تم رفض الطلب.' : 'تم إرسال عرض السعر للعميل بنجاح.',
            'data'    => new ImportRequestResource($updatedRequest),
        ]);
    }

    // قبول عرض السعر من قبل العميل
    public function acceptOffer(Request $request, $id): JsonResponse
    {
        $importRequest = $request->user()->importRequests()->findOrFail($id);

        if ($importRequest->status !== 'offer_sent') {
            return response()->json(['message' => 'لا يمكن اتخاذ إجراء على هذا الطلب حالياً.'], 422);
        }

        $importRequest->update([
            'status' => 'offer_accepted'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم قبول عرض السعر بنجاح وجاري متابعة طلبك.',
            'data' => new ImportRequestResource($importRequest)
        ]);
    }

    // رفض عرض السعر من قبل العميل
    public function rejectOffer(Request $request, $id): JsonResponse
    {
        $importRequest = $request->user()->importRequests()->findOrFail($id);

        if ($importRequest->status !== 'offer_sent') {
            return response()->json(['message' => 'لا يمكن اتخاذ إجراء على هذا الطلب حالياً.'], 422);
        }

        $importRequest->update([
            'status' => 'offer_rejected',
            'rejection_reason' => $request->input('reason', 'تم رفض العرض من قبل العميل')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم رفض عرض السعر.',
            'data' => new ImportRequestResource($importRequest)
        ]);
    }

    public function cancel(Request $request, $id)
    {
        // البحث عن الطلب المباشر التابع للمستخدم الحقيقي
        $importRequest = $request->user()->importRequests()->findOrFail($id);

        // التأكد من أن حالة الطلب ما زالت قابلة للإلغاء
        if ($importRequest->status !== 'pending') {
            return response()->json([
                'message' => 'لا يمكن إلغاء الطلب بعد البدء في معالجته أو مراجعته.'
            ], 422);
        }

        $importRequest->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'message' => 'تم إلغاء طلب الاستيراد بنجاح.'
        ]);
    }

    public function update(StoreImportRequest $request, $id)
    {
        // 1. التثبت من أن الطلب يخص المستخدم
        $importRequest = $request->user()->importRequests()->findOrFail($id);

        // 2. حظر التعديل إذا خرج الطلب من حالة الانتظار
        if ($importRequest->status !== 'pending') {
            return response()->json([
                'message' => 'لا يمكن تعديل الطلب بعد البدء في مراجعته أو تجهيزه.'
            ], 422);
        }

        // 3. تحديث البيانات الأساسية
        $importRequest->update($request->only([
            'currency',
            'phone',
            'address_details',
            'notes'
        ]));

        // 4. إعادة إدخال العناصر (Items)
        if ($request->has('items')) {
            $importRequest->items()->delete(); // حذف العناصر القديمة

            foreach ($request->items as $item) {
                $importRequest->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id'    => $item['unit_id'],
                    'quantity'   => $item['quantity'],
                ]);
            }
        }

        return response()->json([
            'message' => 'تم تعديل طلب الاستيراد بنجاح.',
            'data'    => new ImportRequestResource($importRequest->load(['items.product.images', 'items.unit']))
        ]);
    }
}
