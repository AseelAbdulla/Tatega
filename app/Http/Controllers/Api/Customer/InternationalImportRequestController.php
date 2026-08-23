<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\InternationalImportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

class InternationalImportRequestController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST CUSTOMER REQUESTS
    |--------------------------------------------------------------------------
    |
    | GET /api/customer/international-import
    |
    */

    public function index(Request $request)
    {
        $user = $request->user();

        $requests = InternationalImportRequest::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE INTERNATIONAL IMPORT REQUEST
    |--------------------------------------------------------------------------
    |
    | POST /api/customer/international-import
    |
    | يجب إرسال البيانات باستخدام multipart/form-data
    |
    */

    public function store(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | CHECK INTERNATIONAL CLIENT
        |--------------------------------------------------------------------------
        |
        | إذا كان المستخدم تمت الموافقة عليه سابقًا،
        | فلا يحتاج إلى طلب اعتماد جديد.
        |
        */

        if ($user->hasRole('international-client')) {
            return response()->json([
                'success' => false,

                'message' =>
                    'حسابك مفعل بالفعل كمستورد دولي.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK EXISTING PENDING REQUEST
        |--------------------------------------------------------------------------
        */

        $existingPendingRequest =
            InternationalImportRequest::query()
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->exists();

        if ($existingPendingRequest) {
            return response()->json([
                'success' => false,

                'message' =>
                    'لديك بالفعل طلب اعتماد قيد المراجعة.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
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

            'price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | DOCUMENT
            |--------------------------------------------------------------------------
            */

            'document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | STORE DOCUMENT
        |--------------------------------------------------------------------------
        */

        $documentPath = $request
            ->file('document')
            ->store(
                'international-import-documents',
                'public'
            );


        /*
        |--------------------------------------------------------------------------
        | CREATE REQUEST
        |--------------------------------------------------------------------------
        */

        $importRequest =
            InternationalImportRequest::create([
                'user_id' => $user->id,

                'request_number' =>
                    'IMP-' .
                    strtoupper(
                        Str::random(6)
                    ),

                'title' =>
                    $validated['title'],

                'country' =>
                    $validated['country'],

                'price' =>
                    $validated['price'] ?? null,

                'description' =>
                    $validated['description'] ?? null,

                'document_path' =>
                    $documentPath,

                'status' =>
                    'pending',

                'admin_note' =>
                    'تم استلام طلبك والوثيقة وسيتم مراجعتهما من قبل الإدارة.',

                'rejection_reason' =>
                    null,
            ]);


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' =>
                'تم إرسال طلب اعتماد المستورد والوثيقة بنجاح، والطلب الآن قيد المراجعة.',

            'data' => $importRequest,
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW CUSTOMER REQUEST
    |--------------------------------------------------------------------------
    |
    | GET /api/customer/international-import/{id}
    |
    */

    public function show(
        Request $request,
        InternationalImportRequest $internationalImportRequest
    ) {
        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | OWNERSHIP CHECK
        |--------------------------------------------------------------------------
        */

        if (
            $internationalImportRequest->user_id !==
            $user->id
        ) {
            return response()->json([
                'success' => false,

                'message' =>
                    'غير مصرح لك بعرض هذا الطلب.',
            ], 403);
        }


        return response()->json([
            'success' => true,

            'data' => $internationalImportRequest,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE CUSTOMER REQUEST
    |--------------------------------------------------------------------------
    |
    | PUT /api/customer/international-import/{id}
    |
    */

    public function update(
        Request $request,
        InternationalImportRequest $internationalImportRequest
    ) {
        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | OWNERSHIP CHECK
        |--------------------------------------------------------------------------
        */

        if (
            $internationalImportRequest->user_id !==
            $user->id
        ) {
            return response()->json([
                'success' => false,

                'message' =>
                    'غير مصرح لك بتعديل هذا الطلب.',
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS CHECK
        |--------------------------------------------------------------------------
        */

        if (
            $internationalImportRequest->status !==
            'pending'
        ) {
            return response()->json([
                'success' => false,

                'message' =>
                    'لا يمكن تعديل الطلب بعد بدء مراجعته.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
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

            'price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | DOCUMENT
            |--------------------------------------------------------------------------
            |
            | الوثيقة اختيارية عند التعديل.
            |
            */

            'document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | UPDATE DATA
        |--------------------------------------------------------------------------
        */

        $updateData = [
            'title' =>
                $validated['title'],

            'country' =>
                $validated['country'],

            'price' =>
                $validated['price'] ?? null,

            'description' =>
                $validated['description'] ?? null,
        ];


        /*
        |--------------------------------------------------------------------------
        | REPLACE DOCUMENT
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('document')) {

            /*
            | حذف الوثيقة القديمة
            */

            if (
                $internationalImportRequest->document_path
                &&
                Storage::disk('public')->exists(
                    $internationalImportRequest->document_path
                )
            ) {
                Storage::disk('public')->delete(
                    $internationalImportRequest->document_path
                );
            }


            /*
            | حفظ الوثيقة الجديدة
            */

            $updateData['document_path'] =
                $request
                    ->file('document')
                    ->store(
                        'international-import-documents',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        $internationalImportRequest->update(
            $updateData
        );


        return response()->json([
            'success' => true,

            'message' =>
                'تم تعديل طلب الاعتماد بنجاح.',

            'data' =>
                $internationalImportRequest->fresh(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE CUSTOMER REQUEST
    |--------------------------------------------------------------------------
    |
    | DELETE /api/customer/international-import/{id}
    |
    */

    public function destroy(
        Request $request,
        InternationalImportRequest $internationalImportRequest
    ) {
        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | OWNERSHIP CHECK
        |--------------------------------------------------------------------------
        */

        if (
            $internationalImportRequest->user_id !==
            $user->id
        ) {
            return response()->json([
                'success' => false,

                'message' =>
                    'غير مصرح لك بحذف هذا الطلب.',
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS CHECK
        |--------------------------------------------------------------------------
        */

        if (
            $internationalImportRequest->status !==
            'pending'
        ) {
            return response()->json([
                'success' => false,

                'message' =>
                    'لا يمكن حذف الطلب بعد بدء مراجعته.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | DELETE DOCUMENT
        |--------------------------------------------------------------------------
        */

        if (
            $internationalImportRequest->document_path
            &&
            Storage::disk('public')->exists(
                $internationalImportRequest->document_path
            )
        ) {
            Storage::disk('public')->delete(
                $internationalImportRequest->document_path
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DELETE REQUEST
        |--------------------------------------------------------------------------
        */

        $internationalImportRequest->delete();


        return response()->json([
            'success' => true,

            'message' =>
                'تم حذف طلب الاعتماد بنجاح.',
        ]);
    }
}
