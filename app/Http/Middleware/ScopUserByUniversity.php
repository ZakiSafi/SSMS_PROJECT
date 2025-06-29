<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Teacher;
use App\Models\Student;



class ScopUserByUniversity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   // app/Http/Middleware/ScopeByUniversity.php
public function handle($request, Closure $next)
{
    if ($request->user() && $request->user()->university_id && !$request->user()->hasRole('super-admin')) {
        // Auto-filter these models (add your own)
        
        Teacher::addGlobalScope('university', fn ($q) => 
            $q->where('university_id', $request->user()->university_id)
        );
    }
    return $next($request);
}
}
