<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $model = User::class;
    public function index(Request $request)
    {
        $users = $this->listRecord($request, $this->model, $with = ['roll_permissions']);
        return UserResource::collection($users);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        return $this->storeRecord($request, $this->model);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($this->showRecord($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        return $this->updateRecord($request, $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( User $user)
    {
        return $this->deleteRecord($user);
    }
}
