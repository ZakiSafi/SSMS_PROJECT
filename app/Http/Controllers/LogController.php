<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogRequest;
use App\Http\Resources\LogResource;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    //     public function __construct()
    // {
    //     $this->middleware('permission:logs.view')->only(['index', 'show']);
    //     $this->middleware('permission:logs.edit')->only(['edit', 'update']);
    //     $this->middleware('permission:logs.create')->only(['create', 'store']);
    //     $this->middleware('permission:logs.delete')->only('destroy');
    // }
    /**
     * Display a listing of the resource.
     */

    private $model = Log::class;
    public function index(Request $request)
    {
        
        $log = $this->listRecord($request, $this->model,$withtables = ['users']);
        return LogResource::collection($log);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LogRequest $request)
    {
        return $this->storeRecord($request, $this->model);
    }

    /**
     * Display the specified resource.
     */
    public function show(Log $log)
    {
        return new LogResource($this->showRecord($log));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LogRequest $request, Log $log)
    {
        return $this->updateRecord($request, $log);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Log $log)
    {
        return $this->deleteRecord($log);

    }
}
//
