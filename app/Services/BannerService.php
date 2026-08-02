<?php

namespace App\Services;

use App\Models\Banner;

class BannerService
{
    /**
     * عرض جميع البنرات
     */
    public function index()
    {
        return Banner::all();
    }

    /**
     * إنشاء بنر جديد
     */
    public function store(array $data)
    {
        return Banner::create($data);
    }

    /**
     * عرض بنر واحد
     */
    public function show(Banner $banner)
    {
        return $banner;
    }

    /**
     * تحديث بنر
     */
    public function update(Banner $banner, array $data)
    {
        $banner->update($data);

        return $banner;
    }

    /**
     * حذف بنر
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();

        return true;
    }
}