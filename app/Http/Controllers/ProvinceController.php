<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;
use App\Http\Requests\ProvinceRequest;
use App\Http\Resources\ProvinceResource;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private $model = Province::class;

    public function index(Request $request)
    {


        $provinces =  $this->listRecord($request, $this->model);
        return ProvinceResource::collection($provinces);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProvinceRequest $request)
    {
        return $this->storeRecord($request, $this->model);
    }

    /**
     * Display the specified resource.
     */
    public function show(Province $province)
    {

        $province = $this->showRecord($province);
        return new ProvinceResource($province);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProvinceRequest $request, Province $province)
    {
        return $this->updateRecord($request, $province);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Province $province)
    {
        return $this->deleteRecord($province);
    }
}
