<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\Log;

class LogActivity
{
    public static function add($actionType, $description = null, $table = null, $recordId = null)
    {
        $user = Auth::user();

        if (!$user) return; // Don’t log if no user

        Log::create([
            'user_id' => $user->id,
            'university_id' => $user->university_id ?? null,
            'action_type' => $actionType,
            'action_description' => $description,
            'table_name' => $table,
            'record_id' => $recordId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
