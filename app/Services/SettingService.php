<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function index()
    {
        return Setting::all();
    }


    public function store(array $data)
    {
        return Setting::create($data);
    }


    public function show(Setting $setting)
    {
        return $setting;
    }


    public function update(Setting $setting, array $data)
    {
        $setting->update($data);

        return $setting;
    }


    public function destroy(Setting $setting)
    {
        $setting->delete();

        return true;
    }
}