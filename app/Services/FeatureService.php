<?php

namespace App\Services;

use App\Models\Feature;

class FeatureService
{
    public function index()
    {
        return Feature::all();
    }

    public function store(array $data)
    {
        return Feature::create($data);
    }

    public function show(Feature $feature)
    {
        return $feature;
    }

    public function update(Feature $feature, array $data)
    {
        $feature->update($data);

        return $feature;
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();

        return true;
    }
}