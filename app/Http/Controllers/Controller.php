<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Support\Facades\Storage; // Used to manage file storage (e.g., images)
use Illuminate\Routing\Controller as BaseController; // Base controller class in Laravel
use Illuminate\Support\Facades\Auth; // Facade to access the currently authenticated user
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Trait for handling authorization
use Illuminate\Foundation\Validation\ValidatesRequests; // Trait for validating incoming requests

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


        // 🔍 Build dynamic description from validated data (excluding sensitive fields)
        $excludedFields = ['password'];
        $details = collect($validated)
            ->except($excludedFields)
            ->map(fn($value, $key) => "$key=$value")
            ->implode(', ');

        // 📝 Log the action
        Log::create([
            'user_id' => Auth::id(),
            'university_id' => Auth::user()->university_id ?? null,
            'action_type' => 'create',
            'table_name' => $record->getTable(),
            'record_id' => $record->id,
            'action_description' => "Created " . class_basename($record) . ": $details",
            'ip_address' => $request->ip(),
            'agent' => $request->userAgent(),
        ]);

        return $record; // Return newly created record
    }

    /**
     * Update an existing record and handle image replacement.
     */
    public function updateRecord($request, $record)
    {
        // Store original data before the update
        $oldData = $record->getOriginal();

        // Validate and apply new data
        $validated = $request->validated();
        $record->update($validated);

        // Get changed fields only
        $changedFields = array_diff_assoc($validated, $oldData);

        // If there are changes, build the log description
        if (!empty($changedFields)) {
            $logDetails = collect($changedFields)->map(function ($new, $field) use ($oldData) {
                $old = $oldData[$field] ?? 'null';

                // Handle null and boolean formatting for clarity
                $oldFormatted = is_null($old) ? 'null' : (is_bool($old) ? ($old ? 'true' : 'false') : $old);
                $newFormatted = is_null($new) ? 'null' : (is_bool($new) ? ($new ? 'true' : 'false') : $new);

                return "$field: '$oldFormatted' -> '$newFormatted'";
            })->implode(', ');

            // Create the log entry
            Log::create([
                'user_id' => Auth::id(),
                'university_id' => Auth::user()->university_id ?? null,
                'action_type' => 'update',
                'table_name' => $record->getTable(),
                'record_id' => $record->id,
                'action_description' => "Updated " . class_basename($record) . ": $logDetails",
                'ip_address' => request()->ip(),
                'agent' => request()->userAgent(),
            ]);
        }

        // Optional image handling
        $this->storeImage($request, $record);

        return $record;
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
        // Store original data before deletion (if needed for logging)
        $record->getOriginal();

        // Soft delete the record
        $record->delete();

        // 🔍 Build dynamic description from model attributes (excluding sensitive fields)
        $excludedFields = ['password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'];
        $details = collect($record->getOriginal())
            ->except($excludedFields)
            ->map(fn($value, $key) => "$key=" . (is_null($value) ? 'null' : $value))
            ->implode(', ');

        // 📝 Log the action (correct action_type to 'delete')
        Log::create([
            'user_id' => Auth::id(),
            'university_id' => Auth::user()->university_id ?? null,
            'action_type' => 'delete', // Changed from 'update' to 'delete'
            'table_name' => $record->getTable(),
            'record_id' => $record->id,
            'action_description' => "Deleted " . class_basename($record) . ": $details",
            'ip_address' => request()->ip(), // Fixed: Use `request()` helper instead of undefined `$request`
            'agent' => request()->userAgent(),
        ]);

        $this->deleteImage($record); // Remove associated images
        return response("Record deleted successfully", 202);
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
