<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    /**
     * عرض جميع البنرات
     */
public function index()
{
    return Banner::where('status', 'active')
        ->orderBy('sort_order')
        ->get();
}
    /**
     * إنشاء بنر جديد
     */
   public function store(array $data)
{
    if (isset($data['image'])) {
        $data['image_path'] = $data['image']->store('banners', 'public');

        unset($data['image']);
    }

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
    if (isset($data['image'])) {

        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $data['image_path'] = $data['image']->store('banners', 'public');

        unset($data['image']);
    }

    $banner->update($data);

    return $banner->fresh();
}
    /**
     * حذف بنر
     */
  public function destroy(Banner $banner)
{
    if ($banner->image_path) {
        Storage::disk('public')->delete($banner->image_path);
    }

    $banner->delete();

    return true;
}
}