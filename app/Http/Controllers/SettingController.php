<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\SettingService;
use App\Http\Resources\SettingResource;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;

class SettingController extends Controller
{

    public function __construct(
        protected SettingService $settingService
    ) {}


    public function index()
    {
        return SettingResource::collection(
            $this->settingService->index()
        );
    }


    public function store(StoreSettingRequest $request)
    {
        $setting = $this->settingService->store(
            $request->validated()
        );

        return new SettingResource($setting);
    }


    public function show(Setting $setting)
    {
        return new SettingResource(
            $this->settingService->show($setting)
        );
    }


  public function update(UpdateSettingRequest $request, Setting $setting)
{
    $setting = $this->settingService->update(
        $setting,
        $request->validated()
    );

    return new SettingResource($setting);
}

public function destroy(Setting $setting)
{
    $this->settingService->destroy($setting);

    return response()->json([
        'status' => 'success',
        'message' => 'تم حذف الإعداد بنجاح'
    ]);
}

    
}