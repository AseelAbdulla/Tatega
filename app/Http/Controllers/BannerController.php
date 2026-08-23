<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Services\BannerService;
use App\Http\Resources\BannerResource;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;

class BannerController extends Controller
{
    public function __construct(
        protected BannerService $bannerService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = $this->bannerService->index();

        return BannerResource::collection($banners);
    }

    /**
     * Display only active banners.
     */
    public function active()
    {
        $banners = $this->bannerService->active();

        return BannerResource::collection($banners);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBannerRequest $request)
    {
        $banner = $this->bannerService->store(
            $request->validated()
        );

        return new BannerResource($banner);
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        $banner = $this->bannerService->show($banner);

        return new BannerResource($banner);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateBannerRequest $request,
        Banner $banner
    ) {
        $banner = $this->bannerService->update(
            $banner,
            $request->validated()
        );

        return new BannerResource($banner);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        $this->bannerService->destroy($banner);

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف البنر بنجاح'
        ]);
    }
}