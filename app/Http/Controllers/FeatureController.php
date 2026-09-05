<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Services\FeatureService;
use App\Http\Resources\FeatureResource;
use App\Http\Requests\StoreFeatureRequest;
use App\Http\Requests\UpdateFeatureRequest;

class FeatureController extends Controller
{
    public function __construct(
        protected FeatureService $featureService
    ) {}

    public function index()
    {
        return FeatureResource::collection(
            $this->featureService->index()
        );
    }

    public function store(StoreFeatureRequest $request)
    {
        $feature = $this->featureService->store(
            $request->validated()
        );

        return new FeatureResource($feature);
    }

    public function show(Feature $feature)
    {
        return new FeatureResource(
            $this->featureService->show($feature)
        );
    }

    public function update(UpdateFeatureRequest $request, Feature $feature)
    {
        $feature = $this->featureService->update(
            $feature,
            $request->validated()
        );

        return new FeatureResource($feature);
    }

    public function destroy(Feature $feature)
    {
        $this->featureService->destroy($feature);

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الميزة بنجاح'
        ]);
    }
}