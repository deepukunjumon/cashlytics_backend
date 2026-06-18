<?php

namespace App\Http\Middleware;

use App\Enums\ApiResponseMessage;
use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperadminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== UserRole::Superadmin) {
            return response()->json([
                'success' => false,
                'message' => ApiResponseMessage::Unauthorized->value,
                'data'    => null,
            ], 403);
        }

        return $next($request);
    }
}
