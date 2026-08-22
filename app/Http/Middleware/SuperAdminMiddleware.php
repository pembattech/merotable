<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user(); // works only if auth:sanctum ran before this

        if (!$user || $user->role !== 'root') {
            return response()->json(['message' => 'Unauthorized. Super admin access only.'], 403);
        }

        if (!$request->user()->tokenCan('super-admin')) {
            return response()->json(['message' => 'This token is not authorized for super admin access.'], 403);
        }

        return $next($request);
    }
}
