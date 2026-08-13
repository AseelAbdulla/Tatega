<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
{
    // قراءة اللغة من هيدر Accept-Language من الفرنت إند، والافتراضي 'ar'
    $locale = $request->header('Accept-Language', 'ar');
    if (!in_array($locale, ['ar', 'en'])) {
        $locale = 'ar';
    }

    // دالة مساعدة لتحديد النص المترجم بحسب $locale
    $getLocalized = function ($value) use ($locale) {
        if (is_array($value)) {
            return $value[$locale] ?? $value['ar'] ?? reset($value);
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded[$locale] ?? $decoded['ar'] ?? reset($decoded);
            }
        }
        return $value;
    };

    return [
        'id' => $this->id,
        'product' => [
            'id' => $this->product->id,
            'name' => $getLocalized($this->product->name),
            'sku' => $this->product->sku,
        ],
        
        'unit' => [
            'id' => $this->unit->id,
            'name' => $getLocalized($this->unit->unit_name),
        ],
        'price' => (float) $this->price,
        'quantity' => $this->quantity,
        'subtotal' => (float) ($this->price * $this->quantity),
        'image' => $this->product->images->first()?->image_path,
        'created_at' => $this->created_at,
    ];
}

}
