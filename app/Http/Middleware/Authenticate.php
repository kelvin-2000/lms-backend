<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API requests, don't redirect, just return null
        // This avoids the "Route [login] not defined" error
        if ($request->is('api/*') || $request->expectsJson()) {
            return null;
        }
        
        return route('login');
    }
}
