<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Exists;
use ResponseStatus;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        /// admin controller
    }

    public function listRecord($request, $model, $filter = [], $withTables = null)
    {
        $requests    = $request->all();
        $method      = $request->has('page') ? 'paginate' : 'get';
        $methodValue = $request->has('page') ? $request->get('perPage', 10) : '*';
        $orderColumn = $request->get('order_column', 'id');
        $orderType   = $request->get('order_type', 'desc');


        $query = $withTables ? $model::with($withTables) : $model::query();

        return $query->where(function ($query) use ($requests, $filter) {
            foreach ($requests as $key => $value) {
                if (in_array($key, $filter)) {
                    $query->where($key, 'like', '%' . $value . '%');
                }
            }
        })->orderBy($orderColumn, $orderType)->$method($methodValue);
    }


    public function showRecord($model)
    {
        return $model::findOrFail($model->id);
    }


    public function storeRecord($request, $model)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        $record =  $model::create($validated);
        $this->storeImage($request, $record);
        return $record;
    }

    public function updateRecord($request, $record)
    {

        // $record  = $model::findOrFail($id);
        $this->deleteImage($record);
        $record = tap($record)->update($request->validated());

        $this->storeImage($request, $record);
        return $record;
    }

    public function restore($model, $id)
    {
        $item = $model::withTrashed()->findOrFail($id);
        $item->restore();
    }

    public function forceDelete($model, $id)
    {
        $item = $model::withTrashed()->findOrFail($id);
        $result = $item->forceDelete();
        $this->deleteImage($result);
        return response("Number of rows affected: " . $result, 202);
    }

    public function deleteRecord($record)
    {
        // $record  = $model::findOrFail($id);
        $result = $record->delete();
        $this->deleteImage($record);
        return response("Number of rows affected: " . $result, 202);
    }





    private function storeImage($request, $model)
    {
        if ($model->images == null) return;
        foreach ($model->images as $image) {
            if ($request->hasFile($image)) {
                $imagePath = $request->file($image)->store(class_basename($model), 'public');
                $model->update([$image => $imagePath]);
            }
        }
    }

    private function deleteImage($model)
    {
        if ($model->images == null) return;
        foreach ($model->images as $image) {
            if ($model[$image]) {
                Storage::disk('public')->delete($model[$image]);
                $model->update([$image => null]);
            }
        }
    }
}
