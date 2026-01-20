<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    // ... existing code ...

    /**
     * Convert an authentication exception into a JSON response for API
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // If the request expects JSON (API request)
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.'
        ], 401);
    }
}
