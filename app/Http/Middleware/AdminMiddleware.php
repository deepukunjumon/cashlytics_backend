<?php

namespace App\Http\Middleware;

use App\Enums\ApiResponseMessage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_admin) {
            return response()->json([
                'success' => false,
                'message' => ApiResponseMessage::Unauthorized->value,
                'data'    => null,
            ], 403);
        }

        return $next($request);
    }
}
