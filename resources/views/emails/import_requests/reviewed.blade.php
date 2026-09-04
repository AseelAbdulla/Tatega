
@component('mail::message')
# مرحباً {{ $importRequest->user->name }}

@if($action === 'reject')
نود إعلامك بأنه تم إغلاق ورفض طلب الاستيراد رقم **#{{ $importRequest->id }}**.

**سبب الرفض:** {{ $importRequest->rejection_reason ?? 'لا يوجد' }}
@else
يسرنا إعلامك بأنه تم مراجعة وتسعير طلب الاستيراد رقم **#{{ $importRequest->id }}**.

* **إجمالي المنتجات:** {{ $importRequest->offered_items_total }} {{ $importRequest->currency }}
* **رسوم الشحن:** {{ $importRequest->offered_shipping_fee }} {{ $importRequest->currency }}
* **الإجمالي النهائي:** {{ $importRequest->offered_grand_total }} {{ $importRequest->currency }}

@if($importRequest->offer_expires_at)
> **ملاحظة:** عرض السعر هذا صالِح حتى: {{ $importRequest->offer_expires_at->format('Y-m-d H:i') }}
@endif

@component('mail::button', ['url' => config('app.frontend_url') . '/dashboard/import-requests/' . $importRequest->id])
معاينة العرض والموافقة
@endcomponent
@endif

شكراً لتواصلك معنا،<br>
{{ config('app.name') }}
@endcomponent
