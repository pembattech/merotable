<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        /**
         * Handle 404
         */
        if ($exception instanceof NotFoundHttpException) {

            // ✅ API request → JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.'
                ], 404);
            }

            // ✅ Web request → redirect by guard
            if (auth('restaurant')->check()) {
                return redirect()
                    ->route('restaurant.dashboard')
                    ->with('error', 'Page not found!');
            }

            if (auth('staff')->check()) {
                return redirect()
                    ->route('staff.dashboard')
                    ->with('error', 'Page not found!');
            }

            return redirect('/')
                ->with('error', 'Page not found!');
        }

        return parent::render($request, $exception);
    }


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
