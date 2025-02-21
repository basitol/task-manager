<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthToken
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'Token not provided'], 401);
        }
        return $next($request);
    }
} 