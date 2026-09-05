<?php

namespace App\Services;

use App\Events\ImportRequestCreated;
use App\Models\ImportRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Events\ImportRequestReviewed;

class ImportRequestService
{
    public function createRequest(User $user, array $data): ImportRequest
    {
        return DB::transaction(function () use ($user, $data) {
            $importRequest = $user->importRequests()->create([
                'currency'        => $data['currency'],
                'phone'           => $data['phone'],
                'address_details' => $data['address_details'],
                'notes'           => $data['notes'] ?? null,
                'status'          => 'pending',
            ]);

            foreach ($data['items'] as $item) {
                $importRequest->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id'    => $item['unit_id'],
                    'quantity'   => $item['quantity'],
                ]);
            }

            // إطلاق الحدث لإرسال الإشعار للمدراء
            ImportRequestCreated::dispatch($importRequest);

            return $importRequest->load(['items.product.images', 'items.unit', 'user']);
        });
    }


    public function reviewRequest(ImportRequest $importRequest, array $data): ImportRequest
    {
        return DB::transaction(function () use ($importRequest, $data) {
            $action = $data['action'] ?? 'offer_sent';

            if ($action === 'reject') {
                $importRequest->update([
                    'status'           => 'rejected',
                    'rejection_reason' => $data['rejection_reason'] ?? null,
                    'admin_notes'      => $data['admin_notes'] ?? null,
                ]);
            } else {
                $importRequest->update([
                    'status'               => 'offer_sent',
                    'shipping_method'      => $data['shipping_method'] ?? null,
                    'offered_shipping_fee' => $data['offered_shipping_fee'] ?? 0,
                    'offered_items_total'  => $data['offered_items_total'] ?? 0,
                    'offered_grand_total'  => $data['offered_grand_total'] ?? 0,
                    'offer_expires_at'     => $data['offer_expires_at'] ?? null,
                    'admin_notes'          => $data['admin_notes'] ?? null,
                ]);

                if (isset($data['items'])) {
                    foreach ($data['items'] as $itemData) {
                        $item = $importRequest->items()->where('id', $itemData['id'])->first();

                        if ($item) {
                            $unitPrice = $itemData['offered_unit_price'] ?? 0;
                            $subtotal = $itemData['offered_subtotal'] ?? ($unitPrice * $item->quantity);

                            $item->update([
                                'offered_unit_price' => $unitPrice,
                                'offered_subtotal'   => $subtotal,
                            ]);
                        }
                    }
                }
            }

            $freshRequest = $importRequest->fresh(['items.product.images', 'items.unit', 'user']);

            // إطلاق الحدث لإشعار العميل وتسليمه العرض/الرفض
            ImportRequestReviewed::dispatch($freshRequest, $action);

            return $freshRequest;
        });
    }
}
