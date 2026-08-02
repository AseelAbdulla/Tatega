<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Services\PartnerService;
use App\Http\Resources\PartnerResource;
use App\Http\Requests\StorePartnerRequest;
use App\Http\Requests\UpdatePartnerRequest;

class PartnerController extends Controller
{
    public function __construct(
        protected PartnerService $partnerService
    ) {}


    public function index()
    {
        return PartnerResource::collection(
            $this->partnerService->index()
        );
    }


    public function store(StorePartnerRequest $request)
    {
        $partner = $this->partnerService->store(
            $request->validated()
        );

        return new PartnerResource($partner);
    }


    public function show(Partner $partner)
    {
        return new PartnerResource(
            $this->partnerService->show($partner)
        );
    }


    public function update(UpdatePartnerRequest $request, Partner $partner)
    {
        $partner = $this->partnerService->update(
            $partner,
            $request->validated()
        );

        return new PartnerResource($partner);
    }


    public function destroy(Partner $partner)
    {
        $this->partnerService->destroy($partner);

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الشريك بنجاح'
        ]);
    }
}