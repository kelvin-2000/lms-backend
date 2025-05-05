<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($role === 'admin' && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden - Admin access required'], 403);
        }

        if ($role === 'instructor' && !$request->user()->isInstructorOrAdmin()) {
            return response()->json(['message' => 'Forbidden - Instructor access required'], 403);
        }

        return $next($request);
    }
} 