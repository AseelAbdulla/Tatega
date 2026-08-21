<?php

namespace App\Services;

use App\Models\Partner;
use Illuminate\Support\Facades\Storage;

class PartnerService
{
    public function index()
    {
        return Partner::all();
    }


       public function store(array $data): Partner
    {
        // 📍 هنا يتم فحص الملف وحفظه في مجلد storage/app/public/partners
        if (request()->hasFile('logo')) {
            // يقوم رفعه وإنشاء مجلد partners تلقائياً
            $data['logo'] = request()->file('logo')->store('partners', 'public');
        }

        return Partner::create($data);
    }

    public function update(Partner $partner, array $data): Partner
    {
        if (request()->hasFile('logo')) {
            // حذف الصورة القديمة إن وجدت
            if ($partner->logo) {
                Storage::disk('public')->delete($partner->logo);
            }
            // رفع الصورة الجديدة
            $data['logo'] = request()->file('logo')->store('partners', 'public');
        }

        $partner->update($data);
        return $partner;
    }


    public function show(Partner $partner)
    {
        return $partner;
    }


    public function destroy(Partner $partner)
    {
        $partner->delete();

        return true;
    }
}