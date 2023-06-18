<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, String $role): Response
    {
        if (!auth()->check()) {
            return abort(401);
        }

        if (!auth()->user()->roles()->where('name', $role)->exists()) {
            return abort(403);
        }

        return $next($request);
    }
}
