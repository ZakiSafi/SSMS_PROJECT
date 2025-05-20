<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage; // Used to manage file storage (e.g., images)
use Illuminate\Routing\Controller as BaseController; // Base controller class in Laravel
use Illuminate\Foundation\Validation\ValidatesRequests; // Trait for validating incoming requests
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Trait for handling authorization
use Illuminate\Support\Facades\Auth; // Facade to access the currently authenticated user

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests; // Include traits for validation and authorization

    public function __construct()
    {
        /// Constructor logic can be placed here if needed (e.g., middleware)
    }

    /**
     * List records with optional filters, eager loading, and pagination.
     */
    public function listRecord($request, $model, $filter = [], $withTables = null)
    {
        $requests = $request->all(); // Get all request parameters
        $method = $request->has('page') ? 'paginate' : 'get'; // Determine if pagination is needed
        $methodValue = $request->has('page') ? $request->get('perPage', 10) : '*'; // Get perPage value or use '*'
        $orderColumn = $request->get('order_column', 'id'); // Column to order by (default: id)
        $orderType = $request->get('order_type', 'desc'); // Order direction (default: descending)

        // Build query, optionally with eager-loaded relationships
        $query = $withTables ? $model::with($withTables) : $model::query();

        // Apply filters only for allowed fields
        return $query->where(function ($query) use ($requests, $filter) {
            foreach ($requests as $key => $value) {
                if (in_array($key, $filter)) {
                    $query->where($key, 'like', '%' . $value . '%'); // Apply LIKE filter
                }
            }
        })->orderBy($orderColumn, $orderType)->$method($methodValue); // Order and fetch records
    }

    /**
     * Show a single record by its ID.
     */
    public function showRecord($model)
    {
        return $model::findOrFail($model->id); // Find and return model record or throw 404
    }

    /**
     * Store a new record and handle image upload if defined.
     */
    public function storeRecord($request, $model)
    {
        $validated = $request->validated(); // Get validated request data
        $validated['user_id'] = Auth::id(); // Attach authenticated user's ID
        $record = $model::create($validated); // Create the record
        $this->storeImage($request, $record); // Upload and store associated images
        return $record; // Return newly created record
    }

    /**
     * Update an existing record and handle image replacement.
     */
    public function updateRecord($request, $record)
    {

        $this->deleteImage($record); // Delete existing images from storage
        $record = tap($record)->update($request->validated()); // Update the record with new data
        $this->storeImage($request, $record); // Store new image files
        return $record; // Return updated record
    }

    /**
     * Restore a soft-deleted record by its ID.
     */
    public function restore($model, $id)
    {
        $item = $model::withTrashed()->findOrFail($id); // Find the soft-deleted record
        $item->restore(); // Restore it
    }

    /**
     * Permanently delete a soft-deleted record and remove its images.
     */
    public function forceDelete($model, $id)
    {
        $item = $model::withTrashed()->findOrFail($id); // Find the soft-deleted record
        $result = $item->forceDelete(); // Permanently delete it from database
        $this->deleteImage($item); // Delete its image files
        return response("Number of rows affected: " . $result, 202); // Return success message
    }

    /**
     * Soft delete a record and remove its images from storage.
     */
    public function deleteRecord($record)
    {
        $result = $record->delete(); // Soft delete the record
        $this->deleteImage($record); // Remove images associated with the record
        return response("Number of rows affected: " . $result, 202); // Return success message
    }

    /**
     * Store uploaded image files for the model based on its defined image fields.
     */
    private function storeImage($request, $model)
    {
        if ($model->images == null) return; // Skip if model doesn't define image fields

        foreach ($model->images as $image) { // Loop through each image field
            if ($request->hasFile($image)) { // Check if image file is uploaded
                $imagePath = $request->file($image)->store(class_basename($model), 'public'); // Store the image
                $model->update([$image => $imagePath]); // Save image path to DB
            }
        }
    }

    /**
     * Delete image files from storage and clear their values in the database.
     */
    private function deleteImage($model)
    {
        if ($model->images == null) return; // Skip if model doesn't define image fields

        foreach ($model->images as $image) { // Loop through image fields
            if ($model[$image]) { // If image path is set
                Storage::disk('public')->delete($model[$image]); // Delete the file from public disk
                $model->update([$image => null]); // Set image field to null in database
            }
        }
    }
}
