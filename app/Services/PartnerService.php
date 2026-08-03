<?php

namespace App\Services;

use App\Models\Partner;

class PartnerService
{
    public function index()
    {
        return Partner::all();
    }


    public function store(array $data)
    {
        return Partner::create($data);
    }


    public function show(Partner $partner)
    {
        return $partner;
    }


    public function update(Partner $partner, array $data)
    {
        $partner->update($data);

        return $partner;
    }


    public function destroy(Partner $partner)
    {
        $partner->delete();

        return true;
    }
}