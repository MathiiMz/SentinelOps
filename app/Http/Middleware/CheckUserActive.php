<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->isActive()) {
            $token = $request->user()->currentAccessToken();
            if ($token) {
                $token->delete();
            }

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario inactivo.',
                ], Response::HTTP_FORBIDDEN);
            }

            return redirect()->route('login')
                ->with('error', 'Su cuenta está inactiva. Contacte al administrador.');
        }

        return $next($request);
    }
}
