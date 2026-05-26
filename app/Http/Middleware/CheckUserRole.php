<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || ! $request->user()->hasRole($role)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para acceder a este recurso.',
                ], Response::HTTP_FORBIDDEN);
            }

            abort(403, 'No tiene permiso para acceder a este recurso.');
        }

        return $next($request);
    }
}
