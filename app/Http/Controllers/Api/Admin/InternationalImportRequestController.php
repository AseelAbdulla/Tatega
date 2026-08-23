<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\InternationalImportRequest;
use App\Models\InternalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class InternationalImportRequestController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST ALL INTERNATIONAL IMPORT REQUESTS
    |--------------------------------------------------------------------------
    |
    | GET /api/admin/international-imports
    |
    */

    public function index(Request $request)
    {
        $query = InternationalImportRequest::query()
            ->with([
                'user:id,name,email,phone'
            ])
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | FILTER BY STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $request->validate([
                'status' => [
                    'in:pending,approved,rejected,shipping,delivered'
                ],
            ]);

            $query->where(
                'status',
                $request->status
            );
        }

       $requests = $query->get();

$requests->transform(function ($request) {

    $request->document_url = $request->document_path
        ? url(Storage::url($request->document_path))
        : null;

    return $request;
});

return response()->json([
    'success' => true,

    'data' => $requests,
]);

        /*
        |--------------------------------------------------------------------------
        | ADD DOCUMENT URL
        |--------------------------------------------------------------------------
        */

        $requests->transform(function ($request) {

            $request->document_url = $this->getDocumentUrl(
                $request->document_path
            );

            return $request;
        });

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW ONE REQUEST
    |--------------------------------------------------------------------------
    |
    | GET /api/admin/international-imports/{id}
    |
    */

    public function show(
        InternationalImportRequest $internationalImportRequest
    ) {
        $internationalImportRequest->load([
            'user:id,name,email,phone'
        ]);

        /*
        |--------------------------------------------------------------------------
        | ADD DOCUMENT URL
        |--------------------------------------------------------------------------
        */

        $internationalImportRequest->document_url =
            $this->getDocumentUrl(
                $internationalImportRequest->document_path
            );

        return response()->json([
            'success' => true,
            'data' => $internationalImportRequest,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | APPROVE REQUEST
    |--------------------------------------------------------------------------
    |
    | PATCH /api/admin/international-imports/{id}/approve
    |
    */

    public function approve(
        InternationalImportRequest $internationalImportRequest
    ) {

        /*
        |--------------------------------------------------------------------------
        | STATUS CHECK
        |--------------------------------------------------------------------------
        */

        if (
            $internationalImportRequest->status !== 'pending'
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'لا يمكن الموافقة على هذا الطلب لأن حالته الحالية ليست قيد المراجعة.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $result = DB::transaction(function () use (
            $internationalImportRequest
        ) {

            $user = $internationalImportRequest->user;


            /*
            |--------------------------------------------------------------------------
            | UPDATE REQUEST
            |--------------------------------------------------------------------------
            */

            $internationalImportRequest->update([
                'status' =>
                    'approved',

                'admin_note' =>
                    'تمت الموافقة على طلب اعتماد المستورد.',

                'rejection_reason' =>
                    null,
            ]);


            /*
            |--------------------------------------------------------------------------
            | ASSIGN INTERNATIONAL CLIENT ROLE
            |--------------------------------------------------------------------------
            */

            if (
                !$user->hasRole('international-client')
            ) {
                $user->assignRole(
                    'international-client'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | REMOVE LOCAL CLIENT ROLE
            |--------------------------------------------------------------------------
            */

            if (
                $user->hasRole('local-client')
            ) {
                $user->removeRole(
                    'local-client'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | NOTIFICATION
            |--------------------------------------------------------------------------
            */

            InternalNotification::create([
                'user_id' =>
                    $user->id,

                'title' => [
                    'ar' =>
                        'تمت الموافقة على طلب اعتماد المستورد',

                    'en' =>
                        'International importer request approved',
                ],

                'message' => [
                    'ar' =>
                        'تمت الموافقة على طلب اعتماد المستورد رقم '
                        . $internationalImportRequest->request_number
                        . '. أصبح حسابك الآن مفعلًا كمستورد دولي.',

                    'en' =>
                        'Your international importer request '
                        . $internationalImportRequest->request_number
                        . ' has been approved. Your international importer account is now active.',
                ],

                'type' =>
                    'international_import_approved',

                'is_read' =>
                    false,

                'sent_at' =>
                    now(),
            ]);


            return $internationalImportRequest->fresh([
                'user:id,name,email,phone'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | ADD DOCUMENT URL
        |--------------------------------------------------------------------------
        */

        $result->document_url =
            $this->getDocumentUrl(
                $result->document_path
            );


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' =>
                'تمت الموافقة على طلب اعتماد المستورد وتفعيل حساب العميل الدولي.',

            'data' =>
                $result,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | REJECT REQUEST
    |--------------------------------------------------------------------------
    |
    | PATCH /api/admin/international-imports/{id}/reject
    |
    */

    public function reject(
        Request $request,
        InternationalImportRequest $internationalImportRequest
    ) {

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | STATUS CHECK
        |--------------------------------------------------------------------------
        */

        if (
            $internationalImportRequest->status !== 'pending'
        ) {
            return response()->json([
                'success' => false,

                'message' =>
                    'لا يمكن رفض هذا الطلب لأن حالته الحالية ليست قيد المراجعة.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        $result = DB::transaction(function () use (
            $internationalImportRequest,
            $validated
        ) {

            $user = $internationalImportRequest->user;


            /*
            |--------------------------------------------------------------------------
            | UPDATE REQUEST
            |--------------------------------------------------------------------------
            */

            $internationalImportRequest->update([
                'status' =>
                    'rejected',

                'admin_note' =>
                    'تم رفض طلب اعتماد المستورد.',

                'rejection_reason' =>
                    $validated['rejection_reason'],
            ]);


            /*
            |--------------------------------------------------------------------------
            | NOTIFICATION
            |--------------------------------------------------------------------------
            */

            InternalNotification::create([
                'user_id' =>
                    $user->id,

                'title' => [
                    'ar' =>
                        'تم رفض طلب اعتماد المستورد',

                    'en' =>
                        'International importer request rejected',
                ],

                'message' => [
                    'ar' =>
                        'تم رفض طلب اعتماد المستورد رقم '
                        . $internationalImportRequest->request_number
                        . '. السبب: '
                        . $validated['rejection_reason'],

                    'en' =>
                        'Your international importer request '
                        . $internationalImportRequest->request_number
                        . ' has been rejected. Reason: '
                        . $validated['rejection_reason'],
                ],

                'type' =>
                    'international_import_rejected',

                'is_read' =>
                    false,

                'sent_at' =>
                    now(),
            ]);


            return $internationalImportRequest->fresh([
                'user:id,name,email,phone'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | ADD DOCUMENT URL
        |--------------------------------------------------------------------------
        */

        $result->document_url =
            $this->getDocumentUrl(
                $result->document_path
            );


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'message' =>
                'تم رفض طلب اعتماد المستورد وإرسال إشعار للعميل.',

            'data' =>
                $result,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GET DOCUMENT URL
    |--------------------------------------------------------------------------
    |
    | تحويل document_path إلى رابط يمكن للـFrontend فتحه.
    |
    */

    private function getDocumentUrl(?string $documentPath): ?string
    {
        if (
            empty($documentPath)
        ) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK FILE EXISTS
        |--------------------------------------------------------------------------
        */

        if (
            !Storage::disk('public')->exists($documentPath)
        ) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN PUBLIC URL
        |--------------------------------------------------------------------------
        */

        return Storage::disk('public')->url(
            $documentPath
        );
    }
}
