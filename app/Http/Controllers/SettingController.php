<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Resources\SettingResource;
use App\Models\RollPermission;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $model = Setting::class;
    public function index(Request $request)
    {
        $settings = $this->listRecord($request, $this->model, $with = ['universities']);
        return SettingResource::collection($settings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SettingRequest $request)
    {
        return $this->storeRecord($request, $this->model);

    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        return new SettingResource($this->showRecord($setting));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SettingRequest $request, Setting $setting)
    {
        return $this->updateRecord($request, $setting);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        return $this->deleteRecord($setting);
    }
}
